<?php

namespace App\Services\Payment;

use App\Models\{Company, Order, OrderDetail, CompanySettlement};
use Illuminate\Support\Facades\Log;

class SettlementService
{
    protected float $feePercent;
    protected float $shippingCostPerVendor;
    protected float $freeShippingThreshold;

    public function __construct()
    {
        $this->feePercent             = (float) config('wompi.platform_fee_percent', 5.0);
        $this->shippingCostPerVendor  = (float) config('wompi.shipping_cost_per_vendor', 6000);
        $this->freeShippingThreshold  = (float) config('wompi.free_shipping_threshold', 120000);
    }

    /**
     * Calcula la distribución financiera, comisiones y costos de envío agrupados por empresa vendedora.
     *
     * @param array $vendorItems [company_id => [['product' => Product, 'quantity' => int, 'subtotal' => float]]]
     * @return array [
     *    'vendors' => [company_id => [
     *        'company' => Company,
     *        'subtotal' => float,
     *        'shipping' => float,
     *        'platform_fee' => float,
     *        'net_payout' => float,
     *        'items' => array,
     *    ]],
     *    'summary' => [
     *        'products_subtotal' => float,
     *        'shipping_total' => float,
     *        'platform_fees_total' => float,
     *        'grand_total' => float,
     *        'total_vendor_payout' => float,
     *    ]
     * ]
     */
    public function calculateVendorSettlements(array $vendorItems): array
    {
        $vendors = [];
        $productsSubtotal = 0.0;
        $shippingTotal = 0.0;
        $platformFeesTotal = 0.0;

        // Primero calculamos el subtotal global para evaluar umbral de envío gratuito
        foreach ($vendorItems as $companyId => $items) {
            foreach ($items as $item) {
                $productsSubtotal += (float) ($item['subtotal'] ?? 0);
            }
        }

        $isFreeShipping = ($productsSubtotal >= $this->freeShippingThreshold && $this->freeShippingThreshold > 0);

        foreach ($vendorItems as $companyId => $items) {
            $company = Company::find($companyId);
            $companySubtotal = 0.0;

            foreach ($items as $item) {
                $companySubtotal += (float) ($item['subtotal'] ?? 0);
            }

            // Asignación de costo de envío para este comercio
            $companyShipping = $isFreeShipping ? 0.0 : $this->shippingCostPerVendor;
            $shippingTotal += $companyShipping;

            // Comisión de plataforma (porcentaje sobre ventas de productos)
            $platformFee = round($companySubtotal * ($this->feePercent / 100), 2);
            $platformFeesTotal += $platformFee;

            // Valor neto a favor del comercio (ventas brutas + envío recibido - comisión de plataforma)
            $netPayout = round($companySubtotal + $companyShipping - $platformFee, 2);

            $vendors[$companyId] = [
                'company'      => $company,
                'subtotal'     => $companySubtotal,
                'shipping'     => $companyShipping,
                'platform_fee' => $platformFee,
                'net_payout'   => $netPayout,
                'items'        => $items,
            ];
        }

        $grandTotal = round($productsSubtotal + $shippingTotal, 2);
        $totalVendorPayout = round($productsSubtotal + $shippingTotal - $platformFeesTotal, 2);

        return [
            'vendors' => $vendors,
            'summary' => [
                'products_subtotal'   => $productsSubtotal,
                'shipping_total'      => $shippingTotal,
                'platform_fees_total' => $platformFeesTotal,
                'grand_total'         => $grandTotal,
                'total_vendor_payout' => $totalVendorPayout,
                'is_free_shipping'    => $isFreeShipping,
            ],
        ];
    }

    /**
     * Registra o actualiza la liquidación individualizada para una orden aprobada.
     */
    public function processOrderSettlement(Order $order, string $wompiTransactionId, string $status = CompanySettlement::STATUS_SETTLED): CompanySettlement
    {
        $settlement = CompanySettlement::updateOrCreate(
            ['order_id' => $order->id],
            [
                'company_id'           => $order->company_id,
                'payment_reference'    => $order->payment_reference ?: $order->reference,
                'wompi_transaction_id' => $wompiTransactionId,
                'gross_amount'         => (float) $order->subtotal_company,
                'shipping_amount'      => (float) $order->shipping_cost_company,
                'platform_fee'         => (float) $order->platform_fee,
                'net_payout'           => (float) $order->net_company_amount,
                'status'               => $status,
                'settled_at'           => now(),
                'notes'                => "Liquidación automática vía Webhook Wompi. Transacción: {$wompiTransactionId}",
            ]
        );

        $order->update([
            'transaction_id'           => $wompiTransactionId,
            'settlement_status'        => $status,
            'total_company_settlement' => (float) $order->net_company_amount,
        ]);

        Log::info("Liquidación procesada para orden #{$order->id} (Empresa: {$order->company_id}) por valor neto COP \${$order->net_company_amount}");

        return $settlement;
    }

    /**
     * Cancela la liquidación en caso de que la transacción falle o se cancele.
     */
    public function cancelOrderSettlement(Order $order, string $reason = 'cancelled'): void
    {
        CompanySettlement::where('order_id', $order->id)->update([
            'status' => CompanySettlement::STATUS_FAILED,
            'notes'  => "Liquidación cancelada: {$reason}",
        ]);

        $order->update([
            'settlement_status' => Order::SETTLEMENT_FAILED,
        ]);
    }
}
