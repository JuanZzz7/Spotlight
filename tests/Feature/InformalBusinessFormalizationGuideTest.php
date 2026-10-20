<?php

namespace Tests\Feature;

use App\Models\{Company, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformalBusinessFormalizationGuideTest extends TestCase
{
    use RefreshDatabase;

    private function createInformalMerchant(): array
    {
        $user = User::factory()->create([
            'role'              => 'business',
            'email_verified_at' => now(),
        ]);

        $company = Company::create([
            'user_id'              => $user->id,
            'name'                 => 'Artesanías El Roble',
            'country'              => 'CO',
            'state'                => 'Cundinamarca',
            'city'                 => 'Ubaté',
            'address'              => 'Carrera 7 # 8-20, Ubaté',
            'tipo_negocio'         => 'informal',
            'nombre_comercial'     => 'Artesanías El Roble',
            'cedula_propietario'   => '1070987654',
            'habeas_data_accepted' => true,
            'status'               => 'active',
        ]);

        return [$user, $company];
    }

    /**
     * Test 1: Un comerciante informal puede acceder a la guía de tutoriales de formalización.
     */
    public function test_informal_merchant_can_access_formalization_guide(): void
    {
        [$user, $company] = $this->createInformalMerchant();

        $response = $this->actingAs($user)->get(route('business.formalization.guide'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Tutoriales: Cómo Formalizar tu Negocio');
        $response->assertSee('Aviso Importante: Spotlight NO realiza trámites de formalización');
        $response->assertSee('Nosotros NO gestionamos, NO cobramos, NO intermediamos');
        $response->assertSee('Cámara de Comercio de Bogotá');
        $response->assertSee('DIAN Colombia');
        $response->assertSee('RUES');
        $response->assertSee('Cómo Inscribir el RUT por Primera Vez');
    }

    /**
     * Test 2: En el aviso de facturación para comercios informales se incluye el apartado de tutoriales y el aviso de no intermediación.
     */
    public function test_informal_billing_notice_includes_tutorials_and_disclaimer(): void
    {
        [$user, $company] = $this->createInformalMerchant();

        $response = $this->actingAs($user)->get(route('business.billing.index'));

        $response->assertStatus(200);
        $response->assertSee('Módulo de Facturación Electrónica DIAN');
        $response->assertSee('Comercio Informal / Emprendimiento');
        $response->assertSee('Ver Tutoriales de Formalización');
        $response->assertSee('Aviso Importante: Spotlight NO realiza trámites de formalización');
    }

    /**
     * Test 3: El panel principal del comercio informal muestra el banner informativo de tutoriales.
     */
    public function test_informal_merchant_dashboard_shows_formalization_banner(): void
    {
        [$user, $company] = $this->createInformalMerchant();

        $response = $this->actingAs($user)->get(route('business.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('¿Deseas conocer el proceso para formalizar tu negocio?');
        $response->assertSee('Spotlight no realiza trámites mercantiles ni tributarios');
        $response->assertSee(route('business.formalization.guide'));
    }

    /**
     * Test 4: Un usuario no autenticado o con rol normal no puede acceder a la ruta empresarial de formalización.
     */
    public function test_guest_or_regular_user_cannot_access_formalization_guide(): void
    {
        // Invitado redirige al login
        $guestResponse = $this->get(route('business.formalization.guide'));
        $guestResponse->assertRedirect(route('login'));

        // Usuario estándar no tiene rol business
        $user = User::factory()->create(['role' => 'user']);
        $userResponse = $this->actingAs($user)->get(route('business.formalization.guide'));
        $userResponse->assertStatus(403);
    }
}
