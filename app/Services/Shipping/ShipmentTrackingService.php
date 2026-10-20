<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Support\Facades\DB;

class ShipmentTrackingService
{
    public function __construct(
        protected CarrierApiService $carrierApi
    ) {}

    /**
     * Crea el registro inicial de envío cuando se procesa un pedido en el carrito.
     */
    public function createShipmentForOrder(Order $order, array $attributes): Shipment
    {
        return DB::transaction(function () use ($order, $attributes) {
            $carrier = $attributes['carrier'] ?? 'coordinadora';
            $carrierDetails = $this->carrierApi->getCarrierDetails($carrier);
            $trackingNumber = $attributes['tracking_number'] ?? $this->carrierApi->generateTrackingNumber($carrier);

            $originCity = $order->company->city ?? 'Bogotá D.C.';
            $destinationAddress = $attributes['address'] ?? 'Dirección registrada en pedido';
            
            // Extraer ciudad o defecto
            $destinationCity = $attributes['city'] ?? $this->extractCityFromAddress($destinationAddress) ?: 'Bogotá D.C.';
            $estimatedDelivery = $this->carrierApi->estimateDeliveryDate($originCity, $destinationCity, $carrier);

            $shipment = Shipment::create([
                'order_id'              => $order->id,
                'company_id'            => $order->company_id,
                'tracking_number'       => $trackingNumber,
                'carrier'               => $carrier,
                'carrier_name'          => $carrierDetails['name'],
                'status'                => Shipment::STATUS_PREPARING,
                'origin_city'           => $originCity,
                'destination_city'      => $destinationCity,
                'destination_address'   => $destinationAddress,
                'recipient_name'        => $order->user->name ?? 'Cliente Spotlight',
                'recipient_phone'       => $order->user->phone ?? ($attributes['phone'] ?? null),
                'current_location'      => $originCity . ' (Sede del comercio)',
                'estimated_delivery_at' => $estimatedDelivery,
                'notes'                 => $attributes['notes'] ?? 'Despacho generado automáticamente tras pago confirmado.',
            ]);

            // Registrar evento 1: Pedido confirmado
            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => 'order_confirmed',
                'title'       => 'Pago confirmado y orden registrada',
                'description' => 'Tu compra fue aprobada con éxito. El comercio ha recibido la orden.',
                'location'    => $originCity,
                'occurred_at' => now()->subMinutes(5),
            ]);

            // Registrar evento 2: En preparación
            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => 'preparing',
                'title'       => 'Paquete en preparación y embalaje',
                'description' => 'El vendedor ' . ($order->company->name ?? 'aliado') . ' está empacando y verificando tus productos.',
                'location'    => $originCity,
                'occurred_at' => now(),
            ]);

            return $shipment;
        });
    }

    /**
     * El empresario despacha formalmente el pedido con la transportadora seleccionada.
     */
    public function dispatchShipment(Shipment $shipment, array $data): Shipment
    {
        // VERIFICACIÓN OBLIGATORIA: La transacción debe estar verificada como exitosa antes de despachar
        if ($shipment->order && !$shipment->order->isPaid()) {
            throw new \Exception('No se puede finalizar el proceso de despacho: la transacción de pago de la orden no ha sido verificada como exitosa.');
        }

        return DB::transaction(function () use ($shipment, $data) {
            $carrier = $data['carrier'] ?? $shipment->carrier;
            $carrierDetails = $this->carrierApi->getCarrierDetails($carrier);
            $trackingNumber = !empty($data['tracking_number']) ? trim($data['tracking_number']) : $shipment->tracking_number;

            $shippedAt = now();
            $shipment->update([
                'carrier'          => $carrier,
                'carrier_name'     => $carrierDetails['name'],
                'tracking_number'  => $trackingNumber,
                'status'           => Shipment::STATUS_DISPATCHED,
                'shipped_at'       => $shippedAt,
                'current_location' => $shipment->origin_city . ' (Punto de despacho / Bodega transportadora)',
                'notes'            => $data['notes'] ?? $shipment->notes,
            ]);

            // Actualizar también el status de la orden a 'shipped'
            $shipment->order->update(['status' => 'shipped']);

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => 'dispatched',
                'title'       => 'Paquete recolectado y despachado',
                'description' => "Guía {$trackingNumber} generada formalmente con {$carrierDetails['name']}. El paquete ya salió de la bodega.",
                'location'    => $shipment->origin_city,
                'occurred_at' => $shippedAt,
            ]);

            return $shipment;
        });
    }

    /**
     * Actualiza el estado logístico del envío y añade un nuevo evento a la línea de tiempo.
     */
    public function updateStatus(Shipment $shipment, string $status, ?string $location = null, ?string $description = null): Shipment
    {
        // VERIFICACIÓN OBLIGATORIA: La transacción debe estar verificada como exitosa antes de avanzar en el despliegue
        if (in_array($status, [Shipment::STATUS_DISPATCHED, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_OUT_FOR_DELIVERY, Shipment::STATUS_DELIVERED])) {
            if ($shipment->order && !$shipment->order->isPaid()) {
                throw new \Exception('No se puede avanzar en el despliegue del pedido: la transacción de pago no ha sido verificada como exitosa.');
            }
        }

        return DB::transaction(function () use ($shipment, $status, $location, $description) {
            $now = now();
            $location = $location ?: $shipment->current_location;

            $titles = [
                Shipment::STATUS_PREPARING        => 'En preparación por el comercio',
                Shipment::STATUS_DISPATCHED       => 'Despachado y en custodia de transportadora',
                Shipment::STATUS_IN_TRANSIT       => 'En tránsito nacional hacia destino',
                Shipment::STATUS_OUT_FOR_DELIVERY => 'En vehículo de reparto (Última milla)',
                Shipment::STATUS_DELIVERED        => 'Paquete entregado a satisfacción',
                Shipment::STATUS_FAILED           => 'Novedad o dirección no encontrada',
            ];

            $updateData = [
                'status'           => $status,
                'current_location' => $location,
            ];

            if ($status === Shipment::STATUS_DISPATCHED && !$shipment->shipped_at) {
                $updateData['shipped_at'] = $now;
                $shipment->order->update(['status' => 'shipped']);
            }

            if ($status === Shipment::STATUS_DELIVERED) {
                $updateData['delivered_at'] = $now;
                $shipment->order->update(['status' => 'delivered']);
            }

            $shipment->update($updateData);

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status'      => $status,
                'title'       => $titles[$status] ?? 'Actualización de estado',
                'description' => $description ?: "El paquete cambió a estado '{$shipment->status_label}'.",
                'location'    => $location,
                'occurred_at' => $now,
            ]);

            return $shipment;
        });
    }

    /**
     * Avanza el estado de un envío paso a paso para permitir demostraciones y pruebas en tiempo real.
     */
    public function advanceSimulation(Shipment $shipment): Shipment
    {
        $flow = [
            Shipment::STATUS_PENDING          => Shipment::STATUS_PREPARING,
            Shipment::STATUS_PREPARING        => Shipment::STATUS_DISPATCHED,
            Shipment::STATUS_DISPATCHED       => Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_IN_TRANSIT       => Shipment::STATUS_OUT_FOR_DELIVERY,
            Shipment::STATUS_OUT_FOR_DELIVERY => Shipment::STATUS_DELIVERED,
        ];

        $nextStatus = $flow[$shipment->status] ?? null;
        if (!$nextStatus) {
            return $shipment;
        }

        $messages = [
            Shipment::STATUS_DISPATCHED => [
                'location' => 'Centro Logístico ' . $shipment->origin_city,
                'desc' => 'Paquete admitido en terminal logística de ' . $shipment->carrier_name . '.',
            ],
            Shipment::STATUS_IN_TRANSIT => [
                'location' => 'Ruta Logística Troncal hacia ' . $shipment->destination_city,
                'desc' => 'Vehículo de carga en movimiento intermunicipal hacia terminal destino.',
            ],
            Shipment::STATUS_OUT_FOR_DELIVERY => [
                'location' => $shipment->destination_city . ' (Ruta de última milla)',
                'desc' => 'Paquete cargado en vehículo de entrega local. Llegará a tu dirección hoy.',
            ],
            Shipment::STATUS_DELIVERED => [
                'location' => $shipment->destination_address,
                'desc' => 'Entregado a ' . $shipment->recipient_name . '. Firma y documento verificado.',
            ],
        ];

        $data = $messages[$nextStatus] ?? [
            'location' => $shipment->destination_city,
            'desc' => 'Avance en la red de distribución.',
        ];

        return $this->updateStatus($shipment, $nextStatus, $data['location'], $data['desc']);
    }

    /**
     * Genera la carga útil JSON completa para la API de rastreo en tiempo real.
     */
    public function getTrackingPayload(Shipment $shipment): array
    {
        $carrierDetails = $this->carrierApi->getCarrierDetails($shipment->carrier);
        $liveApiData = $this->carrierApi->fetchTrackingData($shipment->tracking_number);

        return [
            'success'               => true,
            'tracking_number'       => $shipment->tracking_number,
            'carrier'               => [
                'code'      => $shipment->carrier,
                'name'      => $shipment->carrier_name,
                'phone'     => $carrierDetails['phone'] ?? null,
                'website'   => $carrierDetails['website'] ?? null,
                'logo_icon' => $carrierDetails['logo_icon'] ?? 'bi-truck',
                'color'     => $carrierDetails['color'] ?? '#6828E8',
            ],
            'order'                 => [
                'id'       => $shipment->order_id,
                'status'   => $shipment->order->status ?? 'paid',
                'total'    => (float) ($shipment->order->total ?? 0),
                'company'  => $shipment->company->name ?? null,
            ],
            'status'                => [
                'code'               => $shipment->status,
                'label'              => $shipment->status_label,
                'color'              => $shipment->status_color,
                'progress_percent'   => $shipment->progress_percentage,
                'is_delivered'       => $shipment->isDelivered(),
            ],
            'current_location'      => $shipment->current_location,
            'origin_city'           => $shipment->origin_city,
            'destination_city'      => $shipment->destination_city,
            'destination_address'   => $shipment->destination_address,
            'recipient_name'        => $shipment->recipient_name,
            'estimated_delivery_at' => $shipment->estimated_delivery_at?->translatedFormat('d \d\e F, Y - H:i') ?? '1 a 2 días hábiles',
            'shipped_at'            => $shipment->shipped_at?->translatedFormat('d \d\e F, Y - H:i'),
            'delivered_at'          => $shipment->delivered_at?->translatedFormat('d \d\e F, Y - H:i'),
            'live_api'              => $liveApiData,
            'timeline'              => $shipment->events->map(fn($event) => [
                'id'          => $event->id,
                'status'      => $event->status,
                'title'       => $event->title,
                'description' => $event->description,
                'location'    => $event->location,
                'occurred_at' => $event->occurred_at?->translatedFormat('d M, Y - h:i A'),
                'timestamp'   => $event->occurred_at?->toIso8601String(),
                'icon'        => $event->icon,
            ])->values()->all(),
            'server_time'           => now()->toIso8601String(),
        ];
    }

    private function extractCityFromAddress(string $address): ?string
    {
        $knownCities = ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena', 'Bucaramanga', 'Pereira', 'Ubaté', 'Chía', 'Cajicá', 'Zipaquirá', 'Tunja', 'Manizales'];
        foreach ($knownCities as $city) {
            if (stripos($address, $city) !== false) {
                return $city;
            }
        }
        return null;
    }
}
