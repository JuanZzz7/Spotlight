<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Billing\ElectronicDocumentService;
use App\Services\Shipping\ShipmentTrackingService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['company', 'details.product.company', 'electronicDocument', 'shipment.events'])
            ->latest()
            ->paginate(15);

        return view('user.orders.index', compact('orders'));
    }

    public function tracking(Order $order, ShipmentTrackingService $trackingService)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No autorizado para ver el rastreo de este pedido.');
        }

        $shipment = $order->shipment;
        if (!$shipment) {
            $shipment = $trackingService->createShipmentForOrder($order, [
                'address' => 'Dirección de entrega registrada',
                'carrier' => 'coordinadora',
            ]);
        }

        $shipment->load(['events', 'company', 'order.details.product']);

        return view('user.orders.tracking', compact('order', 'shipment'));
    }

    public function invoice(Order $order, ElectronicDocumentService $billingService)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No autorizado.');
        }

        $document = $order->electronicDocument;
        if (!$document) {
            return back()->with('error', 'Este pedido aún no cuenta con factura electrónica emitida por el vendedor.');
        }

        $html = $billingService->renderDocumentHtml($document);
        return response($html)->header('Content-Type', 'text/html');
    }
}
