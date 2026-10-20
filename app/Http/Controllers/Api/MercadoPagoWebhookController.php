<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Order, PaymentLog};
use App\Services\Payment\MercadoPagoGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function __construct(
        protected MercadoPagoGatewayService $gatewayService
    ) {}

    /**
     * Webhook oficial para recibir notificaciones de pago de Mercado Pago (México/Colombia).
     */
    public function handle(Request $request)
    {
        Log::info('Mercado Pago Webhook recibido', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        $country = $request->query('country', 'MX');

        // 1. Validación de autenticidad de firma
        if (!$this->gatewayService->validateWebhookSignature($request, $country)) {
            Log::warning('Mercado Pago Webhook: Firma criptográfica inválida o no autorizada.');
            return response()->json(['error' => 'Firma no válida'], 401);
        }

        $type = $request->input('type') ?: $request->input('topic');
        $dataId = (string) ($request->input('data.id') ?: $request->input('id'));
        $action = (string) ($request->input('action') ?: 'payment.updated');

        if (empty($dataId)) {
            return response()->json(['message' => 'Evento recibido sin identificador de datos.'], 200);
        }

        // 2. Idempotencia y registro del evento
        $paymentLog = PaymentLog::firstOrCreate(
            ['payment_reference' => 'MP-' . $dataId . '-' . $action],
            [
                'provider'   => 'mercadopago',
                'event_type' => $type ?: 'payment',
                'payload'    => $request->all(),
                'status'     => 'received',
            ]
        );

        if (!$paymentLog->wasRecentlyCreated && $paymentLog->status === 'processed') {
            Log::info("Mercado Pago Webhook: Evento {$paymentLog->payment_reference} ya procesado anteriormente (Idempotente).");
            return response()->json(['message' => 'Evento ya procesado (Idempotente).'], 200);
        }

        // 3. Procesar estado según tipo de evento
        if (in_array($type, ['payment', 'charge'])) {
            $paymentStatus = $request->input('data.status') ?: $request->input('status', 'approved');
            $externalRef   = (string) ($request->input('data.external_reference') ?: $request->input('external_reference'));

            $orders = Order::where('payment_reference', $externalRef)
                ->orWhere('transaction_id', $dataId)
                ->orWhere('reference', $externalRef)
                ->get();

            foreach ($orders as $order) {
                if ($paymentStatus === 'approved') {
                    if ($order->payment_status !== Order::PAYMENT_APPROVED) {
                        $order->update([
                            'payment_status'    => Order::PAYMENT_APPROVED,
                            'order_status'      => Order::ORDER_CONFIRMED,
                            'status'            => 'paid',
                            'settlement_status' => Order::SETTLEMENT_SETTLED,
                            'transaction_id'    => $dataId,
                        ]);
                    }
                } elseif (in_array($paymentStatus, ['rejected', 'cancelled'])) {
                    if ($order->payment_status !== Order::PAYMENT_DECLINED) {
                        $order->update([
                            'payment_status' => Order::PAYMENT_DECLINED,
                            'order_status'   => Order::ORDER_CANCELLED,
                            'status'         => 'cancelled',
                        ]);
                    }
                }
            }
        }

        $paymentLog->update(['status' => 'processed']);

        return response()->json(['message' => 'Webhook procesado exitosamente.'], 200);
    }
}
