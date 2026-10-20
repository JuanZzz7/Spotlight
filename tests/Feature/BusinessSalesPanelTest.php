<?php

namespace Tests\Feature;

use App\Models\BillingConfiguration;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessSalesPanelTest extends TestCase
{
    use RefreshDatabase;

    private function createMerchantWithOrders(): array
    {
        $user = User::factory()->create([
            'role' => 'business',
            'email' => 'merchant_' . uniqid() . '@spotlight.local',
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Comercio Artesanal ' . uniqid(),
            'tipo_negocio' => 'formal',
            'razon_social' => 'Comercio SAS',
            'nit' => '901234567-1',
            'status' => 'active',
            'address' => 'Cra 7 # 8-45, Ubaté',
            'phone' => '3101234567',
            'email' => 'comercio@spotlight.local',
        ]);

        CompanyFiscalProfile::create([
            'company_id' => $company->id,
            'fiscal_status' => CompanyFiscalProfile::STATUS_ENABLED,
            'person_type' => 'persona_juridica',
            'id_type' => '31',
            'identification_number' => '901234567',
            'dv' => '1',
            'business_name' => $company->razon_social,
            'commercial_name' => $company->name,
            'tax_regime' => 'responsable_iva',
            'tax_responsibilities' => ['O-13'],
            'ciiu_code' => '4719',
            'fiscal_address' => $company->address,
            'municipality_code' => '25843',
            'department_code' => '25',
            'fiscal_email' => $company->email,
            'fiscal_phone' => $company->phone,
        ]);

        BillingConfiguration::create([
            'company_id' => $company->id,
            'provider' => 'sandbox',
            'environment' => 'sandbox',
            'resolution_number' => '18760000001',
            'resolution_prefix' => 'SETP',
            'range_from' => 1,
            'range_to' => 5000,
            'current_consecutive' => 100,
            'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
            'resolution_date_from' => now()->subDay(),
            'resolution_date_to' => now()->addYear(),
            'credit_note_prefix' => 'NC',
            'credit_note_consecutive' => 10,
            'is_connected' => true,
        ]);

        $buyer = User::factory()->create([
            'role' => 'user',
            'name' => 'Carlos Cliente',
            'email' => 'carlos@cliente.local',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Queso Pera Ubaté',
            'price' => 25000,
            'stock' => 50,
            'active' => true,
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'company_id' => $company->id,
            'reference' => 'SPOT-TEST-9988',
            'payment_reference' => 'WOMPI-REF-9988',
            'payment_method' => 'card',
            'payment_status' => 'approved',
            'order_status' => 'confirmed',
            'total' => 50000,
            'subtotal_company' => 50000,
            'net_company_amount' => 47500,
            'settlement_status' => 'settled',
            'status' => 'paid',
            'billing_status' => 'not_billed',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'company_id' => $company->id,
            'product_name' => $product->name,
            'product_sku' => 'SKU-QUESO-01',
            'quantity' => 2,
            'unit_price' => 25000,
            'subtotal' => 50000,
            'tax_type' => '01',
            'tax_rate' => 19.00,
            'tax_amount' => 7983.19,
        ]);

        return [$user, $company, $order, $buyer, $product];
    }

    public function test_merchant_can_access_sales_panel_with_stats_and_orders(): void
    {
        [$user, $company, $order, $buyer] = $this->createMerchantWithOrders();

        $response = $this->actingAs($user)->get(route('business.billing.sales'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Ventas y Pedidos');
        $response->assertSee('SPOT-TEST-9988');
        $response->assertSee('Carlos Cliente');
        $response->assertSee('$50.000');
        $response->assertSee('$47.500');
        $response->assertSee('Facturar DIAN');
        $response->assertSee('Detalle');
    }

    public function test_merchant_can_filter_sales_by_search_and_fiscal_status(): void
    {
        [$user, $company, $order] = $this->createMerchantWithOrders();

        // Búsqueda por referencia existente
        $res = $this->actingAs($user)->get(route('business.billing.sales', ['search' => 'SPOT-TEST-9988']));
        $res->assertStatus(200);
        $res->assertSee('SPOT-TEST-9988');

        // Búsqueda inexistente
        $resNotFound = $this->actingAs($user)->get(route('business.billing.sales', ['search' => 'REF-INEXISTENTE-1234']));
        $resNotFound->assertStatus(200);
        $resNotFound->assertSee('No se encontraron ventas registradas');

        // Filtro por estado no facturado
        $resNotBilled = $this->actingAs($user)->get(route('business.billing.sales', ['billing_status' => 'not_billed']));
        $resNotBilled->assertStatus(200);
        $resNotBilled->assertSee('SPOT-TEST-9988');
    }

    public function test_merchant_can_export_sales_to_csv(): void
    {
        [$user, $company, $order] = $this->createMerchantWithOrders();

        $response = $this->actingAs($user)->get(route('business.billing.sales', ['export' => 'csv']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $content = $response->getContent();
        $this->assertStringContainsString('SPOT-TEST-9988', $content);
        $this->assertStringContainsString('Carlos Cliente', $content);
        $this->assertStringContainsString('ID,Referencia,Fecha,Cliente', $content);
    }

    public function test_merchant_can_access_business_dashboard_and_alias_route(): void
    {
        [$user, $company, $order] = $this->createMerchantWithOrders();

        // Dashboard principal del empresario
        $dashResponse = $this->actingAs($user)->get(route('business.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Empresario - Spotlight');
        $dashResponse->assertSee('Actividad de Ventas');
        $dashResponse->assertSee('Queso Pera Ubaté');

        // Ruta alias de ventas
        $salesAlias = $this->actingAs($user)->get(route('business.sales'));
        $salesAlias->assertStatus(200);
        $salesAlias->assertSee('Panel de Ventas y Pedidos');
    }
}
