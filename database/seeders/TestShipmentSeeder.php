<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestShipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Obtener o crear comprador de prueba (Ana García)
            $buyer = User::firstOrCreate(
                ['email' => 'ana@spotlight.com'],
                [
                    'name' => 'Ana García',
                    'password' => bcrypt('password'),
                    'role' => 'user',
                ]
            );

            // 2. Obtener la empresa Lácteos San Diego Ubaté
            $company = Company::where('name', 'like', '%Lácteos%')
                ->orWhere('email', 'lacteos@spotlight.com')
                ->first();

            if (!$company) {
                $company = Company::first();
            }

            // 3. Obtener o crear productos de la empresa
            $products = Product::where('company_id', $company->id)->take(2)->get();
            if ($products->isEmpty()) {
                $p1 = Product::create([
                    'company_id' => $company->id,
                    'name' => 'Queso Pera Artesanal de Ubaté (500g)',
                    'description' => 'Delicioso queso pera artesanal fresco elaborado en el Valle de Ubaté.',
                    'price' => 18500,
                    'stock' => 25,
                    'active' => true,
                    'featured' => true,
                ]);
                $p2 = Product::create([
                    'company_id' => $company->id,
                    'name' => 'Arequipe Tradicional de Finca (250g)',
                    'description' => 'Dulce de leche tradicional elaborado con leche fresca pura de Ubaté.',
                    'price' => 12000,
                    'stock' => 30,
                    'active' => true,
                    'featured' => true,
                ]);
                $products = collect([$p1, $p2]);
            }

            // 4. Crear u obtener el pedido de prueba
            $orderReference = 'SPOT-ORD-TEST-UBATE-01';
            $order = Order::updateOrCreate(
                ['reference' => $orderReference],
                [
                    'user_id' => $buyer->id,
                    'company_id' => $company->id,
                    'country' => 'CO',
                    'currency' => 'COP',
                    'payment_reference' => 'SPOT-PAY-TEST-UBATE',
                    'transaction_id' => 'TRX-WOMPI-TEST-998811',
                    'payment_method' => 'Tarjeta de Crédito / Wompi',
                    'payment_gateway' => 'wompi',
                    'payment_status' => 'approved',
                    'order_status' => 'confirmed',
                    'status' => 'shipped',
                    'billing_status' => 'not_billed',
                    'payment_method_code' => '48',
                    'subtotal_company' => 30500.00,
                    'shipping_cost_company' => 6000.00,
                    'platform_fee' => 1525.00,
                    'net_company_amount' => 34975.00,
                    'settlement_status' => 'settled',
                    'total_company_settlement' => 34975.00,
                    'total' => 36500.00,
                    'shipping_address' => [
                        'recipient_name' => 'Ana García Murcia',
                        'email' => 'ana@spotlight.com',
                        'phone' => '3123495823',
                        'doc_type' => 'CC',
                        'doc_number' => '1056256845',
                        'country' => 'Colombia',
                        'department' => 'Cundinamarca',
                        'city' => 'Bogotá D.C.',
                        'neighborhood' => 'Chicó Norte',
                        'address' => 'Carrera 15 # 93-40',
                        'complement' => 'Apto 402',
                        'delivery_notes' => 'Entregar en portería - paquete con empaque térmico',
                        'billing_address' => 'Carrera 15 # 93-40',
                    ],
                ]
            );

            // 5. Detalles de la orden
            OrderDetail::where('order_id', $order->id)->delete();
            $subtotalSum = 0;
            foreach ($products as $index => $prod) {
                $qty = 1;
                $price = $prod->price > 0 ? $prod->price : 15000;
                $itemSubtotal = $price * $qty;
                $subtotalSum += $itemSubtotal;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'company_id' => $company->id,
                    'product_name' => $prod->name,
                    'product_sku' => $prod->sku ?? ('SKU-' . $prod->id),
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $itemSubtotal,
                    'tax_type' => '01',
                    'tax_rate' => 0.00,
                    'tax_amount' => 0.00,
                ]);
            }

            // 6. Guía oficial de prueba para test de rastreo
            $trackingNumber = 'CRD-261022-UBATE';
            $now = Carbon::now();

            $shipment = Shipment::updateOrCreate(
                ['tracking_number' => $trackingNumber],
                [
                    'order_id' => $order->id,
                    'company_id' => $company->id,
                    'carrier' => 'coordinadora',
                    'carrier_name' => 'Coordinadora Mercantil',
                    'status' => Shipment::STATUS_IN_TRANSIT,
                    'origin_city' => 'Ubaté, Cundinamarca',
                    'destination_city' => 'Bogotá D.C.',
                    'destination_address' => 'Carrera 15 # 93-40, Chicó Norte, Apto 402, Bogotá D.C.',
                    'recipient_name' => 'Ana García Murcia',
                    'recipient_phone' => '3123495823',
                    'current_location' => 'Centro Logístico Siberia / Cota (En ruta a Bogotá)',
                    'estimated_delivery_at' => $now->copy()->addDay()->setTime(17, 30),
                    'shipped_at' => $now->copy()->subHours(6),
                    'notes' => 'Despacho prioritario refrigerado de productos lácteos artesanales de Ubaté.',
                ]
            );

            // 7. Eventos de la línea de tiempo de rastreo
            ShipmentEvent::where('shipment_id', $shipment->id)->delete();

            $events = [
                [
                    'status' => 'order_confirmed',
                    'title' => 'Pago confirmado y orden registrada',
                    'description' => 'Tu compra fue aprobada y confirmada con éxito por la pasarela de pagos. El comercio ha recibido la orden.',
                    'location' => 'Ubaté, Cundinamarca',
                    'occurred_at' => $now->copy()->subHours(8),
                ],
                [
                    'status' => 'preparing',
                    'title' => 'Paquete embalado en empaque térmico',
                    'description' => 'El productor ' . $company->name . ' preparó los productos con refrigeración y sello de garantía artesanal.',
                    'location' => 'Vereda San Diego, Finca El Paraíso, Ubaté',
                    'occurred_at' => $now->copy()->subHours(6)->subMinutes(30),
                ],
                [
                    'status' => 'dispatched',
                    'title' => 'Entregado a Coordinadora Mercantil',
                    'description' => 'Guía oficial asignada. El vehículo recolector recibió el paquete en el centro de acopio de Ubaté.',
                    'location' => 'Sede Coordinadora Ubaté (Calle 8 # 6-30)',
                    'occurred_at' => $now->copy()->subHours(4),
                ],
                [
                    'status' => 'in_transit',
                    'title' => 'En tránsito hacia Centro de Distribución Regional',
                    'description' => 'El paquete viaja en camión refrigerado por el corredor Ubaté - Chía - Bogotá. En proceso de pesaje y ruteo a última milla.',
                    'location' => 'Centro Logístico Siberia / Cota (Autopista Medellín Km 2.5)',
                    'occurred_at' => $now->copy()->subHours(1),
                ],
            ];

            foreach ($events as $evt) {
                ShipmentEvent::create([
                    'shipment_id' => $shipment->id,
                    'status' => $evt['status'],
                    'title' => $evt['title'],
                    'description' => $evt['description'],
                    'location' => $evt['location'],
                    'occurred_at' => $evt['occurred_at'],
                ]);
            }
        });
    }
}
