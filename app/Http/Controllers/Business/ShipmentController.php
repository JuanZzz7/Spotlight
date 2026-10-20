<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Shipping\CarrierApiService;
use App\Services\Shipping\ShipmentTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentTrackingService $trackingService,
        protected CarrierApiService $carrierApi
    ) {}

    public function index()
    {
        $company = Auth::user()->company;
        if (!$company) {
            return redirect()->route('business.company.edit')->with('warning', 'Primero configura los datos de tu empresa.');
        }

        // Aseguramos que órdenes que aún no tengan shipment creado lo tengan
        $unassignedOrders = Order::where('company_id', $company->id)
            ->whereDoesntHave('shipment')
            ->get();

        foreach ($unassignedOrders as $ord) {
            $this->trackingService->createShipmentForOrder($ord, [
                'address' => 'Dirección registrada en el pedido',
                'carrier' => 'coordinadora',
            ]);
        }

        $shipments = Shipment::where('company_id', $company->id)
            ->with(['order.user', 'order.details.product', 'events'])
            ->latest()
            ->paginate(15);

        $carriers = CarrierApiService::CARRIERS;

        return view('business.shipments.index', compact('company', 'shipments', 'carriers'));
    }

    public function dispatchOrder(Request $request, Shipment $shipment)
    {
        $company = Auth::user()->company;
        if ($shipment->company_id !== $company->id) {
            abort(403, 'No autorizado para despachar este paquete.');
        }

        // VERIFICACIÓN OBLIGATORIA:
        // No se puede finalizar el proceso de despacho sin que la transacción de pago haya sido verificada como exitosa
        if ($shipment->order && !$shipment->order->isPaid()) {
            return back()->with('error', 'No se puede despachar ni finalizar este proceso: la transacción de pago de la orden no ha sido verificada como exitosa (Estado: ' . strtoupper($shipment->order->payment_status ?: 'PENDIENTE') . ').');
        }

        $request->validate([
            'carrier'         => 'required|string',
            'tracking_number' => 'nullable|string|max:60',
            'notes'           => 'nullable|string|max:500',
        ]);

        $trackingNumber = $request->tracking_number;
        if (empty($trackingNumber)) {
            $trackingNumber = $this->carrierApi->generateTrackingNumber($request->carrier);
        }

        $this->trackingService->dispatchShipment($shipment, [
            'carrier'         => $request->carrier,
            'tracking_number' => $trackingNumber,
            'notes'           => $request->notes,
        ]);

        return back()->with('success', "¡Pedido despachado con éxito! Guía asignada: {$trackingNumber} con {$shipment->carrier_name}.");
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $company = Auth::user()->company;
        if ($shipment->company_id !== $company->id) {
            abort(403, 'No autorizado.');
        }

        // VERIFICACIÓN OBLIGATORIA: No avanzar en el despliegue de entrega si el pago no fue exitoso
        if (in_array($request->status, ['dispatched', 'in_transit', 'out_for_delivery', 'delivered'])) {
            if ($shipment->order && !$shipment->order->isPaid()) {
                return back()->with('error', 'Acción bloqueada: No se puede finalizar ni avanzar en el despliegue del pedido porque el pago no está verificado como exitoso.');
            }
        }

        $request->validate([
            'status'      => 'required|string|in:preparing,dispatched,in_transit,out_for_delivery,delivered,failed',
            'location'    => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        $this->trackingService->updateStatus(
            $shipment,
            $request->status,
            $request->location,
            $request->description
        );

        return back()->with('success', "Estado del envío actualizado a '{$shipment->status_label}' en tiempo real.");
    }
}
