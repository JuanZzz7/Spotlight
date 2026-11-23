<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\User;
use App\Services\Shipping\ShipmentTrackingService;
use Database\Seeders\TestShipmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Company $company;
    private Product $product;
    private Order $order;
    private Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'business', 'email_verified_at' => now()]);
        $this->company = Company::create([
            'user_id' => $this->seller->id,
            'name' => 'Textiles Andinos',
            'nit' => '900987654-1',
            'city' => 'Bogotá D.C.',
            'status' => 'active',
            'is_formal' => true,
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Ruana de Lana Virgen',
            'price' => 180000,
            'stock' => 15,
            'active' => true,
        ]);

        $this->buyer = User::factory()->create([
            'role' => 'user',
            'name' => 'Valeria Gómez',
            'email_verified_at' => now(),
        ]);

        $this->order = Order::create([
            'user_id' => $this->buyer->id,
            'company_id' => $this->company->id,
            'total' => 180000,
            'status' => 'paid',
            'payment_status' => Order::PAYMENT_APPROVED,
            'payment_method_code' => '48',
        ]);

        OrderDetail::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'company_id' => $this->company->id,
            'quantity' => 1,
            'unit_price' => 180000,
            'subtotal' => 180000,
            'product_name' => $this->product->name,
        ]);

        $service = app(ShipmentTrackingService::class);
        $this->shipment = $service->createShipmentForOrder($this->order, [
            'address' => 'Carrera 15 # 85-30, Chapinero, Bogotá D.C.',
            'carrier' => 'coordinadora',
        ]);
    }

    public function test_shipment_is_created_with_events_and_tracking_number(): void
    {
        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'order_id' => $this->order->id,
            'company_id' => $this->company->id,
            'carrier' => 'coordinadora',
            'carrier_name' => 'Coordinadora Mercantil',
            'status' => 'preparing',
        ]);

        $this->assertNotEmpty($this->shipment->tracking_number);
        $this->assertGreaterThanOrEqual(2, $this->shipment->events()->count());
    }

    public function test_public_tracking_api_returns_live_payload_by_number(): void
    {
        $response = $this->getJson("/api/v1/tracking/{$this->shipment->tracking_number}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'tracking_number' => $this->shipment->tracking_number,
            'carrier' => [
                'code' => 'coordinadora',
                'name' => 'Coordinadora Mercantil',
            ],
            'status' => [
                'code' => 'preparing',
                'label' => 'En Preparación y Empaque',
                'is_delivered' => false,
            ],
        ]);

        $response->assertJsonStructure([
            'success',
            'tracking_number',
            'carrier',
            'status' => ['code', 'label', 'color', 'progress_percent', 'is_delivered'],
            'origin_city',
            'destination_city',
            'timeline',
        ]);
    }

    public function test_public_tracking_api_returns_404_for_invalid_number(): void
    {
        $response = $this->getJson('/api/v1/tracking/GUIA-INEXISTENTE-999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_buyer_can_access_order_tracking_api(): void
    {
        $response = $this->actingAs($this->buyer)->getJson("/api/v1/orders/{$this->order->id}/tracking");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'tracking_number' => $this->shipment->tracking_number,
        ]);
    }

    public function test_user_cannot_access_another_users_order_tracking(): void
    {
        $otherUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($otherUser)->getJson("/api/v1/orders/{$this->order->id}/tracking");

        $response->assertStatus(403);
    }

    public function test_merchant_can_dispatch_shipment_with_carrier(): void
    {
        $response = $this->actingAs($this->seller)->post(route('business.shipments.dispatch', $this->shipment), [
            'carrier' => 'servientrega',
            'tracking_number' => 'SER-2026-998877',
            'notes' => 'Despachado en sede principal.',
        ]);

        $response->assertSessionHas('success');
        $this->shipment->refresh();

        $this->assertEquals('dispatched', $this->shipment->status);
        $this->assertEquals('servientrega', $this->shipment->carrier);
        $this->assertEquals('SER-2026-998877', $this->shipment->tracking_number);
        $this->assertEquals('shipped', $this->order->fresh()->status);
        $this->assertNotNull($this->shipment->shipped_at);
    }

    public function test_merchant_can_update_status_and_event_timeline(): void
    {
        $response = $this->actingAs($this->seller)->post(route('business.shipments.status', $this->shipment), [
            'status' => 'out_for_delivery',
            'location' => 'Bogotá D.C. - Zona Chapinero',
            'description' => 'Repartidor asignado en ruta de entrega.',
        ]);

        $response->assertSessionHas('success');
        $this->shipment->refresh();

        $this->assertEquals('out_for_delivery', $this->shipment->status);
        $this->assertEquals('Bogotá D.C. - Zona Chapinero', $this->shipment->current_location);

        $this->assertDatabaseHas('shipment_events', [
            'shipment_id' => $this->shipment->id,
            'status' => 'out_for_delivery',
            'title' => 'En vehículo de reparto (Última milla)',
            'location' => 'Bogotá D.C. - Zona Chapinero',
        ]);
    }

    public function test_real_time_simulation_endpoint_advances_shipment_status(): void
    {
        // En estado inicial 'preparing', avanzar debe pasarlo a 'dispatched'
        $response = $this->postJson("/api/v1/shipments/{$this->shipment->id}/simulate-advance");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => [
                'code' => 'dispatched',
            ],
        ]);

        $this->assertEquals('dispatched', $this->shipment->fresh()->status);
    }

    public function test_buyer_web_tracking_view_renders_correctly(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('user.orders.tracking', $this->order));

        $response->assertStatus(200);
        $response->assertSee($this->shipment->tracking_number);
        $response->assertSee('Coordinadora Mercantil');
        $response->assertSee('Rastreo de Envío');
    }

    public function test_public_tracking_portal_page(): void
    {
        $response = $this->get(route('public.tracking', ['guia' => $this->shipment->tracking_number]));

        $response->assertStatus(200);
        $response->assertSee($this->shipment->tracking_number);
        $response->assertSee('Coordinadora Mercantil');
    }

    public function test_merchant_cannot_dispatch_shipment_if_payment_not_verified(): void
    {
        // Cambiar la orden a no pagada (pending / unverified)
        $this->order->update([
            'status'         => 'pending',
            'payment_status' => Order::PAYMENT_PENDING,
        ]);

        $response = $this->actingAs($this->seller)
            ->post(route('business.shipments.dispatch', $this->shipment), [
                'carrier' => 'servientrega',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals('preparing', $this->shipment->fresh()->status);
    }

    public function test_public_tracking_portal_shows_not_found_for_invalid_guide(): void
    {
        $response = $this->get(route('public.tracking', ['guia' => 'GUIA-INEXISTENTE-999']));

        $response->assertStatus(200);
        $response->assertSee('Guía no encontrada');
        $response->assertSee('GUIA-INEXISTENTE-999');
    }

    public function test_public_tracking_portal_renders_without_parameters(): void
    {
        $response = $this->get(route('public.tracking'));

        $response->assertStatus(200);
        $response->assertSee('Rastrea tu Paquete en Tiempo Real');
        $response->assertDontSee('Guía no encontrada');
    }

    public function test_simulation_advances_through_all_lifecycle_stages_until_delivered(): void
    {
        // Estado inicial: preparing -> avanzar a dispatched
        $this->postJson("/api/v1/shipments/{$this->shipment->id}/simulate-advance")
            ->assertStatus(200)
            ->assertJsonPath('status.code', 'dispatched');

        // dispatched -> in_transit
        $this->postJson("/api/v1/shipments/{$this->shipment->id}/simulate-advance")
            ->assertStatus(200)
            ->assertJsonPath('status.code', 'in_transit');

        // in_transit -> out_for_delivery
        $this->postJson("/api/v1/shipments/{$this->shipment->id}/simulate-advance")
            ->assertStatus(200)
            ->assertJsonPath('status.code', 'out_for_delivery');

        // out_for_delivery -> delivered
        $this->postJson("/api/v1/shipments/{$this->shipment->id}/simulate-advance")
            ->assertStatus(200)
            ->assertJsonPath('status.code', 'delivered')
            ->assertJsonPath('status.is_delivered', true);

        $freshShipment = $this->shipment->fresh();
        $this->assertEquals('delivered', $freshShipment->status);
        $this->assertNotNull($freshShipment->delivered_at);
        $this->assertEquals('delivered', $this->order->fresh()->status);
    }

    public function test_test_shipment_seeder_creates_official_ubate_guide_and_can_be_tracked(): void
    {
        $seeder = new TestShipmentSeeder();
        $seeder->run();

        $this->assertDatabaseHas('shipments', [
            'tracking_number' => 'CRD-261022-UBATE',
            'carrier' => 'coordinadora',
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        // Consulta mediante API pública
        $apiResponse = $this->getJson('/api/v1/tracking/CRD-261022-UBATE');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'success' => true,
            'tracking_number' => 'CRD-261022-UBATE',
            'carrier' => [
                'code' => 'coordinadora',
                'name' => 'Coordinadora Mercantil',
            ],
            'status' => [
                'code' => 'in_transit',
            ],
            'origin_city' => 'Ubaté, Cundinamarca',
            'destination_city' => 'Bogotá D.C.',
        ]);

        // Consulta en portal público web
        $webResponse = $this->get(route('public.tracking', ['guia' => 'CRD-261022-UBATE']));
        $webResponse->assertStatus(200);
        $webResponse->assertSee('CRD-261022-UBATE');
        $webResponse->assertSee('Coordinadora Mercantil');
        $webResponse->assertSee('Ubaté, Cundinamarca');
        $webResponse->assertSee('Bogotá D.C.');
    }
}
