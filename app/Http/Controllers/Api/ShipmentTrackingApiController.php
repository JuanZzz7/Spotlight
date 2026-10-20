<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Shipping\ShipmentTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentTrackingApiController extends Controller
{
    public function __construct(
        protected ShipmentTrackingService $trackingService
    ) {}

    /**
     * API Pública de Rastreo en Tiempo Real por número de guía.
     * Ejemplo: GET /api/v1/tracking/CRD-20261019-8A1B2
     */
    public function trackByNumber(string $trackingNumber): JsonResponse
    {
        $trackingNumber = trim($trackingNumber);

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->with(['order.user', 'company', 'events'])
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => "No se encontró ningún envío asociado al número de guía '{$trackingNumber}'. Por favor verifica el código.",
            ], 404);
        }

        $payload = $this->trackingService->getTrackingPayload($shipment);

        return response()->json($payload);
    }

    /**
     * API para usuarios autenticados: rastrear el envío de su orden en tiempo real.
     * Ejemplo: GET /api/v1/orders/{order}/tracking
     */
    public function trackOrder(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Verificar permisos: el comprador, el vendedor o un administrador
        if ($user && $order->user_id !== $user->id && $order->company?->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para ver el rastreo de este pedido.',
            ], 403);
        }

        // Si la orden no tiene aún envío inicializado (ej. órdenes previas), lo creamos
        $shipment = $order->shipment;
        if (!$shipment) {
            $shipment = $this->trackingService->createShipmentForOrder($order, [
                'address' => 'Dirección registrada en pedido',
                'carrier' => 'coordinadora',
            ]);
        }

        $shipment->load(['order.user', 'company', 'events']);
        $payload = $this->trackingService->getTrackingPayload($shipment);

        return response()->json($payload);
    }

    /**
     * API de simulación de avance en tiempo real (para pruebas interactivas o demostración).
     * Ejemplo: POST /api/v1/shipments/{shipment}/simulate-advance
     */
    public function simulateAdvance(Shipment $shipment): JsonResponse
    {
        $shipment = $this->trackingService->advanceSimulation($shipment);
        $shipment->load(['order.user', 'company', 'events']);

        $payload = $this->trackingService->getTrackingPayload($shipment);
        $payload['simulation_message'] = "¡El envío avanzó exitosamente a '{$shipment->status_label}' en tiempo real!";

        return response()->json($payload);
    }
}
