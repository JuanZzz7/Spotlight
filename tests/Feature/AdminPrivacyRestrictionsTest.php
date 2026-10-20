<?php

namespace Tests\Feature;

use App\Models\{Company, Order, Product, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrivacyRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test 1: El panel de administrador no muestra productos, órdenes ni ingresos de empresas.
     */
    public function test_admin_dashboard_does_not_contain_products_or_revenue_metrics(): void
    {
        $admin = $this->createAdmin();

        $merchant = User::factory()->create(['role' => 'business']);
        $company = Company::create([
            'user_id'              => $merchant->id,
            'name'                 => 'Comercio Confidencial',
            'tipo_negocio'         => 'formal',
            'address'              => 'Calle 10 # 5-20',
            'habeas_data_accepted' => true,
            'status'               => 'active',
        ]);

        Product::create([
            'company_id'  => $company->id,
            'name'        => 'Producto Privado de Empresa',
            'description' => 'Detalle confidencial',
            'price'       => 999999,
            'stock'       => 10,
            'active'      => true,
        ]);

        Order::create([
            'user_id'    => $admin->id,
            'company_id' => $company->id,
            'total'      => 999999,
            'status'     => 'paid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // No debe mostrar métricas financieras ni catálogos de productos
        $response->assertDontSee('Productos en Catálogo');
        $response->assertDontSee('Volumen Transaccional');
        $response->assertDontSee('Ventas Realizadas');
        $response->assertDontSee('$999.999');

        // Debe mostrar únicamente indicadores de gobernanza administrativa
        $response->assertSee('Usuarios Registrados');
        $response->assertSee('Emprendedores / Comercios');
        $response->assertSee('Empresas en Plataforma');
        $response->assertSee('Verificaciones DIAN');
        $response->assertSee('Solicitudes Internacionales');
    }

    /**
     * Test 2: El menú lateral del administrador no tiene accesos a productos ni reportes financieros.
     */
    public function test_admin_sidebar_does_not_have_products_or_reports_links(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('>Gestión de productos<');
        $response->assertDontSee('>Reportes generales<');
    }

    /**
     * Test 3: Intentar ingresar a /admin/products o /admin/reports redirige al panel principal del administrador.
     */
    public function test_admin_products_and_reports_redirect_to_dashboard(): void
    {
        $admin = $this->createAdmin();

        $prodResponse = $this->actingAs($admin)->get(route('admin.products.index'));
        $prodResponse->assertRedirect(route('admin.dashboard'));

        $repResponse = $this->actingAs($admin)->get(route('admin.reports.index'));
        $repResponse->assertRedirect(route('admin.dashboard'));
    }
}
