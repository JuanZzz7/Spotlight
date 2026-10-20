<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Order, PaymentLog, Product, Shipment};
use App\Services\Payment\{SettlementService, WompiGatewayService};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WompiWebhookController extends Controller
{
    protected WompiGatewayService $wompiService;
    protected SettlementService $settlementService;

    public function __construct(
        WompiGatewayService $wompiService,
        SettlementService $settlementService
    ) {
        $this->wompiService = $wompiService;
        $this->settlementService = $settlementService;
    }

    /**
     * Endpoint oficial para recepción de eventos y webhooks de Wompi Colombia.
     * Documentación: https://docs.wompi.co/docs/colombia/eventos-transacciones/
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        // 1. Registro de auditoría inmediato
        $log = PaymentLog::create([
            'order_id'          => null,
            'payment_reference' => $payload['data']['transaction']['reference'] ?? null,
            'provider'          => 'wompi',
            'event_type'        => $payload['event'] ?? 'unknown',
            'payload'           => $this->sanitizePayloadForLog($payload),
            'ip_address'        => $request->ip(),
            'status'            => 'received',
        ]);

        // 2. Validación de Firma Criptográfica de Evento
        if (!$this->wompiService->validateWebhookSignature($payload)) {
            Log::warning('Wompi Webhook: Firma criptográfica inválida rechazada', [
                'ip'        => $request->ip(),
                'signature' => $payload['signature'] ?? null,
            ]);

            $log->update(['status' => 'invalid_signature']);

            return response()->json([
                'error'   => 'Firma criptográfica inválida o no autorizada.',
                'code'    => 'INVALID_CHECKSUM',
            ], 401);
        }

        // Solo procesamos eventos de actualización de transacción
        $event = $payload['event'] ?? '';
        if ($event !== 'transaction.updated') {
            $log->update(['status' => 'ignored_event']);
            return response()->json(['status' => 'ignored', 'message' => "Evento {$event} ignorado."], 200);
        }

        $transaction = $payload['data']['transaction'] ?? [];
        $transactionId = $transaction['id'] ?? null;
        $reference = $transaction['reference'] ?? null;
        $status = strtoupper($transaction['status'] ?? '');
        $amountInCents = (int) ($transaction['amount_in_cents'] ?? 0);
        $paymentMethodType = $transaction['payment_method_type'] ?? 'WOMPI';

        if (!$reference) {
            $log->update(['status' => 'missing_reference']);
            return response()->json(['error' => 'Referencia no encontrada en la transacción'], 200);
        }

        // 3. Búsqueda de órdenes vinculadas a la referencia
        $orders = Order::byPaymentReference($reference)->with('details.product')->get();

        if ($orders->isEmpty()) {
            Log::warning("Wompi Webhook: No se encontraron órdenes para la referencia {$reference}");
            $log->update(['status' => 'orders_not_found']);
            return response()->json(['status' => 'not_found', 'message' => 'Órdenes no encontradas en el sistema'], 200);
        }

        $log->update(['order_id' => $orders->first()->id]);

        // 4. Idempotencia: Si ya fue procesado en este mismo estado, responder 200 de inmediato
        $allAlreadyProcessed = $orders->every(function ($order) use ($status, $transactionId) {
            if ($status === 'APPROVED' && $order->payment_status === Order::PAYMENT_APPROVED && $order->transaction_id === $transactionId) {
                return true;
            }
            if (in_array($status, ['DECLINED', 'ERROR']) && $order->payment_status === Order::PAYMENT_DECLINED) {
                return true;
            }
            return false;
        });

        if ($allAlreadyProcessed) {
            $log->update(['status' => 'idempotent_duplicate']);
            return response()->json([
                'status'  => 'already_processed',
                'message' => 'La transacción ya había sido procesada anteriormente de forma idempotente.',
            ], 200);
        }

        // 5. Verificación estricta de monto en centavos contra la base de datos
        $expectedTotalCents = (int) round($orders->sum('total') * 100);
        if ($expectedTotalCents !== $amountInCents) {
            Log::critical("ALERTA DE SEGURIDAD: Discrepancia de monto en Wompi! Esperado: {$expectedTotalCents} cents, Recibido: {$amountInCents} cents.", [
                'reference'      => $reference,
                'transaction_id' => $transactionId,
            ]);

            $log->update(['status' => 'amount_mismatch']);

            foreach ($orders as $order) {
                $order->update([
                    'payment_status' => Order::PAYMENT_ERROR,
                    'transaction_id' => $transactionId,
                ]);
            }

            return response()->json([
                'error'   => 'Discrepancia crítica de monto en la transacción.',
                'code'    => 'AMOUNT_MISMATCH',
            ], 200);
        }

        // 6. Transición de estados y liquidación por empresa
        switch ($status) {
            case 'APPROVED':
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status'      => Order::PAYMENT_APPROVED,
                        'status'              => 'paid',
                        'order_status'        => Order::ORDER_IN_PREPARATION,
                        'transaction_id'      => $transactionId,
                        'payment_method'      => $paymentMethodType,
                        'payment_method_code' => $this->mapPaymentMethodCode($paymentMethodType),
                    ]);

                    // Desencadena la liquidación contable individualizada a favor de la empresa
                    $this->settlementService->processOrderSettlement($order, $transactionId, 'settled');

                    // Notifica o avanza el envío
                    if ($shipment = $order->shipment) {
                        $shipment->update([
                            'status' => Shipment::STATUS_PREPARING,
                        ]);
                    }
                }

                $log->update(['status' => 'payment_approved']);
                Log::info("Wompi Webhook: Pago APROBADO para {$orders->count()} órdenes (Ref: {$reference}, Tx: {$transactionId})");
                break;

            case 'DECLINED':
            case 'ERROR':
            case 'VOIDED':
                foreach ($orders as $order) {
                    // Restaurar existencias si no estaban canceladas previamente
                    if ($order->order_status !== Order::ORDER_CANCELLED) {
                        foreach ($order->details as $detail) {
                            if ($product = Product::find($detail->product_id)) {
                                $product->increment('stock', $detail->quantity);
                            }
                        }
                    }

                    $order->update([
                        'payment_status' => Order::PAYMENT_DECLINED,
                        'status'         => 'cancelled',
                        'order_status'   => Order::ORDER_CANCELLED,
                        'transaction_id' => $transactionId,
                        'payment_method' => $paymentMethodType,
                    ]);

                    $this->settlementService->cancelOrderSettlement($order, "Wompi: {$status}");
                }

                $log->update(['status' => 'payment_declined']);
                Log::notice("Wompi Webhook: Pago RECHAZADO/CANCELADO (Ref: {$reference}, Status: {$status})");
                break;

            case 'PENDING':
                foreach ($orders as $order) {
                    $order->update([
                        'payment_status' => Order::PAYMENT_PENDING,
                        'order_status'   => Order::ORDER_PENDING_PAYMENT,
                        'transaction_id' => $transactionId,
                        'payment_method' => $paymentMethodType,
                    ]);
                }
                $log->update(['status' => 'payment_pending']);
                break;

            default:
                Log::info("Wompi Webhook: Estado no manejado {$status}");
                break;
        }

        return response()->json([
            'status'        => 'success',
            'orders_count'  => $orders->count(),
            'wompi_status'  => $status,
        ], 200);
    }

    /**
     * Mapea el tipo de método de pago de Wompi al código estándar DIAN.
     */
    protected function mapPaymentMethodCode(string $wompiMethod): string
    {
        return match (strtoupper($wompiMethod)) {
            'CARD'         => '48', // Tarjeta Crédito
            'PSE'          => '31', // Transferencia Débito Bancaria
            'NEQUI'        => '49', // Monedero / Tarjeta Débito electrónica
            'BANCOLOMBIA'  => '31', // Transferencia
            default        => '10', // Efectivo / Otro
        };
    }

    /**
     * Limpia datos sensibles para no almacenar información de tarjetas ni secretos.
     */
    protected function sanitizePayloadForLog(array $payload): array
    {
        if (isset($payload['data']['transaction']['payment_method']['extra']['card_number'])) {
            $payload['data']['transaction']['payment_method']['extra']['card_number'] = '************' . substr($payload['data']['transaction']['payment_method']['extra']['card_number'], -4);
        }
        if (isset($payload['data']['transaction']['payment_method']['token'])) {
            $payload['data']['transaction']['payment_method']['token'] = '[REDACTED]';
        }
        return $payload;
    }
}
