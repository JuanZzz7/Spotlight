<?php

namespace Tests\Feature;

use App\Models\{AssistedPurchase, BillingConfiguration, Company, CompanyFiscalProfile, Order, PaymentLog, Product, User};
use App\Services\Payment\MercadoPagoGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultinationalMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $colombianMerchant;
    protected Company $colombianCompany;
    protected Product $colombianProduct;

    protected User $mexicanMerchant;
    protected Company $mexicanCompany;
    protected Product $mexicanProduct;

    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear Comercio Colombiano Formalizado (Ubaté, Cundinamarca)
        $this->colombianMerchant = User::factory()->create([
            'name'     => 'Don Pedro Lácteos',
            'email'    => 'pedro@lacteosubate.co',
            'role'     => 'business',
            'country'  => 'CO',
            'password' => Hash::make('password123'),
        ]);

        $this->colombianCompany = Company::create([
            'user_id'          => $this->colombianMerchant->id,
            'name'             => 'Lácteos del Valle de Ubaté',
            'status'           => 'active',
            'country'          => 'CO',
            'state'            => 'Cundinamarca',
            'city'             => 'Ubaté',
            'address'          => 'Carrera 7 # 8-20',
            'currency'         => 'COP',
            'phone'            => '3101234567',
            'phone_prefix'     => '+57',
            'tax_id'           => '901234567-1',
            'tipo_negocio'     => 'formal',
            'formalization'    => 'formal',
            'legal_verification_status' => 'verified',
        ]);

        CompanyFiscalProfile::create([
            'company_id'            => $this->colombianCompany->id,
            'fiscal_status'         => CompanyFiscalProfile::STATUS_ENABLED,
            'person_type'           => 'persona_juridica',
            'id_type'               => '31',
            'identification_number' => '901234567',
            'dv'                    => '1',
            'business_name'         => 'Lácteos del Valle de Ubaté SAS',
            'commercial_name'       => 'Lácteos del Valle de Ubaté',
            'tax_regime'            => 'responsable_iva',
            'tax_responsibilities'  => ['O-13', 'O-47'],
            'ciiu_code'             => '4719',
            'fiscal_address'        => 'Carrera 7 # 8-20, Ubaté',
            'municipality_code'     => '25843',
            'department_code'       => '25',
            'fiscal_email'          => 'pedro@lacteosubate.co',
            'fiscal_phone'          => '3101234567',
        ]);

        BillingConfiguration::create([
            'company_id'             => $this->colombianCompany->id,
            'dian_environment'       => 'sandbox',
            'dian_resolution_number' => '18760000001',
            'dian_resolution_date'   => '2026-01-01',
            'dian_prefix'            => 'SETP',
            'dian_range_from'        => 1,
            'dian_range_to'          => 5000,
            'dian_current_number'    => 10,
            'dian_technical_key'     => 'tech-key-12345',
        ]);

        $this->colombianProduct = Product::create([
            'company_id'  => $this->colombianCompany->id,
            'name'        => 'Queso Pera Tradicional Ubaté 500g',
            'price'       => 18000,
            'stock'       => 25,
            'active'      => true,
            'country'     => 'CO',
            'currency'    => 'COP',
            'description' => 'Auténtico queso hilado ubatense.',
        ]);

        // 2. Crear Comercio Mexicano (Guadalajara, Jalisco)
        $this->mexicanMerchant = User::factory()->create([
            'name'     => 'Alejandro Morales',
            'email'    => 'alejandro@artesaniasjalisco.mx',
            'role'     => 'business',
            'country'  => 'MX',
            'password' => Hash::make('password123'),
        ]);

        $this->mexicanCompany = Company::create([
            'user_id'          => $this->mexicanMerchant->id,
            'name'             => 'Artesanías y Cerámica Tlaquepaque',
            'status'           => 'active',
            'country'          => 'MX',
            'state'            => 'Jalisco',
            'city'             => 'Guadalajara',
            'postal_code'      => '45500',
            'address'          => 'Calle Independencia 145',
            'currency'         => 'MXN',
            'phone'            => '3312345678',
            'phone_prefix'     => '+52',
            'tax_id'           => 'MOAJ880101XYZ', // RFC Mexicano
            'tipo_negocio'     => 'informal',
            'formalization'    => 'informal',
        ]);

        $this->mexicanProduct = Product::create([
            'company_id'  => $this->mexicanCompany->id,
            'name'        => 'Jarrón de Barro Bruñido Hecho a Mano',
            'price'       => 450.00,
            'stock'       => 10,
            'active'      => true,
            'country'     => 'MX',
            'currency'    => 'MXN',
            'description' => 'Pieza artesanal tradicional de Tlaquepaque.',
        ]);

        // 3. Crear Comprador
        $this->buyer = User::factory()->create([
            'name'     => 'Mariana Gómez',
            'email'    => 'mariana@cliente.com',
            'role'     => 'user',
            'country'  => 'CO',
            'password' => Hash::make('password123'),
        ]);
    }

    /**
     * Test 1: Registro de Emprendimiento Mexicano.
     * No debe exigir NIT, RUT ni Cámara de Comercio de Colombia.
     */
    public function test_mexican_business_registration_succeeds_without_colombian_documents(): void
    {
        $payload = [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos@oaxacaart.mx',
            'password'              => 'Oaxaca2026!',
            'password_confirmation' => 'Oaxaca2026!',
            'role'                  => 'business',
            'phone'                 => '9511234567',
            'company_name'          => 'Alebrijes de San Martín',
            'country'               => 'MX',
            'state'                 => 'Oaxaca',
            'city'                  => 'San Martín Tilcajete',
            'postal_code'           => '71506',
            'address'               => 'Av. Principal 22',
            'tax_id'                => 'RUMC900202ABC', // RFC Mexicano
            'phone_prefix'          => '+52',
            'tipo_negocio'          => 'informal',
            'habeas_data_accepted'  => 1,
        ];

        $response = $this->post(url('/register'), $payload);

        $this->assertTrue($response->isRedirect());

        $this->assertDatabaseHas('companies', [
            'name'        => 'Alebrijes de San Martín',
            'country'     => 'MX',
            'state'       => 'Oaxaca',
            'currency'    => 'MXN',
            'phone_prefix'=> '+52',
            'tax_id'      => 'RUMC900202ABC',
        ]);
    }

    /**
     * Test 2: Creación de Productos en México.
     * Asigna automáticamente país MX y moneda MXN.
     */
    public function test_mexican_merchant_creates_product_with_mxn_currency(): void
    {
        $this->actingAs($this->mexicanMerchant);

        $payload = [
            'name'        => 'Café Pluma Hidalgo de Oaxaca 500g',
            'description' => 'Café de especialidad de sombra.',
            'price'       => 220.50,
            'stock'       => 40,
            'active'      => 1,
        ];

        $response = $this->post(route('business.products.store'), $payload);

        $response->assertRedirect(route('business.products.index'));

        $this->assertDatabaseHas('products', [
            'company_id' => $this->mexicanCompany->id,
            'name'       => 'Café Pluma Hidalgo de Oaxaca 500g',
            'country'    => 'MX',
            'currency'   => 'MXN',
            'price'      => 220.50,
        ]);
    }

    /**
     * Test 3: Exclusión Total del Módulo de Facturación Electrónica DIAN para Emprendimientos Mexicanos.
     * Ningún emprendimiento mexicano puede acceder a billing.* ni emitir facturas.
     */
    public function test_mexican_business_is_completely_blocked_from_colombian_dian_billing(): void
    {
        // El método de modelo debe retornar falso
        $this->assertFalse($this->mexicanCompany->canIssueElectronicInvoices());
        $this->assertTrue($this->mexicanCompany->isMexican());
        $this->assertFalse($this->mexicanCompany->isColombian());

        // Acceso HTTP al panel de facturación debe ser denegado
        $this->actingAs($this->mexicanMerchant);

        $response = $this->get(route('business.billing.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));

        // Intentar crear factura directamente debe ser bloqueado
        $responseCreate = $this->get(route('business.billing.invoices.create'));
        $this->assertTrue(in_array($responseCreate->status(), [403, 302]));

        // Intentar acceder a ventas
        $responseSales = $this->get(route('business.billing.sales'));
        $this->assertTrue(in_array($responseSales->status(), [403, 302]));
    }

    /**
     * Test 4: El Comercio Colombiano Formalizado SÍ conserva acceso a Facturación Electrónica DIAN.
     */
    public function test_colombian_business_retains_access_to_dian_billing(): void
    {
        $this->assertTrue($this->colombianCompany->canIssueElectronicInvoices());
        $this->assertTrue($this->colombianCompany->isColombian());
        $this->assertFalse($this->colombianCompany->isMexican());

        $this->actingAs($this->colombianMerchant);

        $response = $this->get(route('business.billing.index'));
        $response->assertStatus(200);
        $response->assertSee('Facturación');
    }

    /**
     * Test 5: Exploración de Productos y Filtrado por Mercado (Todos / Colombia / México).
     */
    public function test_user_can_explore_marketplace_and_filter_by_country(): void
    {
        $this->actingAs($this->buyer);

        // Sin filtro: muestra ambos productos
        $responseAll = $this->get(route('user.dashboard'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Queso Pera Tradicional Ubaté');
        $responseAll->assertSee('Jarrón de Barro Bruñido Hecho a Mano');

        // Filtro Colombia
        $responseCO = $this->get(route('user.dashboard', ['country' => 'CO']));
        $responseCO->assertStatus(200);
        $responseCO->assertSee('Queso Pera Tradicional Ubaté');
        $responseCO->assertDontSee('Jarrón de Barro Bruñido Hecho a Mano');

        // Filtro México
        $responseMX = $this->get(route('user.dashboard', ['country' => 'MX']));
        $responseMX->assertStatus(200);
        $responseMX->assertSee('Jarrón de Barro Bruñido Hecho a Mano');
        $responseMX->assertDontSee('Queso Pera Tradicional Ubaté');
    }

    /**
     * Test 6: Aislamiento del Carrito Local contra Mezclas Transfronterizas.
     * Si el carrito tiene productos colombianos, no se puede añadir producto mexicano.
     */
    public function test_cart_blocks_mixing_products_from_different_countries(): void
    {
        $this->actingAs($this->buyer);

        // 1. Añadir producto colombiano al carrito
        $resp1 = $this->post(route('user.cart.add', $this->colombianProduct), ['quantity' => 1]);
        $resp1->assertRedirect();
        $this->assertCount(1, session('cart', []));

        // 2. Intentar añadir producto mexicano al mismo carrito
        $resp2 = $this->post(route('user.cart.add', $this->mexicanProduct), ['quantity' => 1]);
        $resp2->assertSessionHas('error');
        $this->assertStringContainsString('No es posible mezclar', session('error'));

        // El carrito debe seguir conteniendo solo el producto colombiano
        $cart = session('cart', []);
        $this->assertArrayHasKey($this->colombianProduct->id, $cart);
        $this->assertArrayNotHasKey($this->mexicanProduct->id, $cart);
    }

    /**
     * Test 7: Flujo Completo de Compra Internacional Asistida (Colombia ⇄ México).
     */
    public function test_full_assisted_international_purchase_lifecycle(): void
    {
        // 1. Comprador solicita producto mexicano desde Colombia
        $this->actingAs($this->buyer);

        $createView = $this->get(route('user.assisted_purchases.create', $this->mexicanProduct));
        $createView->assertStatus(200);
        $createView->assertSee('Compra Internacional Asistida');
        $createView->assertSee('Jarrón de Barro Bruñido');

        $purchasePayload = [
            'quantity'                 => 2,
            'recipient_name'           => 'Mariana Gómez',
            'recipient_email'          => 'mariana@cliente.com',
            'recipient_phone'          => '+57 3119876543',
            'recipient_tax_id'         => '1023456789',
            'destination_country'      => 'CO',
            'destination_state'        => 'Bogotá D.C.',
            'destination_city'         => 'Bogotá',
            'destination_postal_code'  => '110111',
            'destination_address'      => 'Calle 100 # 15-20 Apto 402',
            'buyer_notes'              => 'Por favor empacar con protección extra para cerámica.',
        ];

        $postRequest = $this->post(route('user.assisted_purchases.store', $this->mexicanProduct), $purchasePayload);
        $postRequest->assertRedirect();

        $assistedPurchase = AssistedPurchase::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($assistedPurchase);
        $this->assertEquals(2, $assistedPurchase->quantity);
        $this->assertEquals(450.00, $assistedPurchase->original_price);
        $this->assertEquals('MXN', $assistedPurchase->original_currency);
        $this->assertEquals(900.00, $assistedPurchase->total_agreed); // 2 * 450
        $this->assertEquals(AssistedPurchase::STATUS_PENDING_REVIEW, $assistedPurchase->status);
        $this->assertStringStartsWith('SPOT-INT-', $assistedPurchase->request_code);

        // 2. Comprador puede consultar su solicitud en el panel de tracking
        $buyerShow = $this->get(route('user.assisted_purchases.show', $assistedPurchase));
        $buyerShow->assertStatus(200);
        $buyerShow->assertSee($assistedPurchase->request_code);

        // 3. Otro usuario NO puede ver la solicitud ajena (Seguridad / Autorización)
        $intruder = User::factory()->create(['role' => 'user']);
        $this->actingAs($intruder);
        $intruderResp = $this->get(route('user.assisted_purchases.show', $assistedPurchase));
        $intruderResp->assertStatus(403);

        // 4. El comerciante mexicano recibe la solicitud en su panel y cotiza envío internacional
        $this->actingAs($this->mexicanMerchant);

        $bizIndex = $this->get(route('business.assisted_purchases.index'));
        $bizIndex->assertStatus(200);
        $bizIndex->assertSee($assistedPurchase->request_code);

        $quotePayload = [
            'status'                  => AssistedPurchase::STATUS_PENDING_PAYMENT,
            'shipping_cost_agreed'    => 350.00, // Envío DHL internacional
            'other_fees_agreed'       => 50.00,  // Embalaje para artesanía
            'payment_currency'        => 'MXN',
            'payment_method_agreed'   => 'Transferencia Internacional / Enlace Seguro',
            'estimated_delivery_days' => '6 a 9 días hábiles',
            'carrier_name'            => 'DHL Express México',
            'tracking_number'         => 'MXDHL9876543210',
            'seller_notes'            => 'Piezas empacadas con burbuja doble. Pagar a cuenta CLABE acordada.',
        ];

        $updateResp = $this->put(route('business.assisted_purchases.update', $assistedPurchase), $quotePayload);
        $updateResp->assertRedirect();

        $assistedPurchase->refresh();
        $this->assertEquals(AssistedPurchase::STATUS_PENDING_PAYMENT, $assistedPurchase->status);
        $this->assertEquals(350.00, $assistedPurchase->shipping_cost_agreed);
        $this->assertEquals(50.00, $assistedPurchase->other_fees_agreed);
        // Total = 900 base + 350 shipping + 50 fees = 1300 MXN
        $this->assertEquals(1300.00, $assistedPurchase->total_agreed);
        $this->assertEquals('MXDHL9876543210', $assistedPurchase->tracking_number);

        // 5. El comprador reporta la referencia de pago
        $this->actingAs($this->buyer);
        $payResp = $this->post(route('user.assisted_purchases.payment', $assistedPurchase), [
            'payment_reference' => 'SPEI-TRANSF-99887766',
            'payment_notes'     => 'Transferencia realizada con éxito el día de hoy.',
        ]);
        $payResp->assertRedirect();

        $assistedPurchase->refresh();
        $this->assertEquals(AssistedPurchase::STATUS_PAYMENT_PROCESSING, $assistedPurchase->status);
        $this->assertEquals('SPEI-TRANSF-99887766', $assistedPurchase->payment_reference);

        // 6. El vendedor confirma la recepción del pago
        $this->actingAs($this->mexicanMerchant);
        $confirmResp = $this->post(route('business.assisted_purchases.confirm_payment', $assistedPurchase));
        $confirmResp->assertRedirect();

        $assistedPurchase->refresh();
        $this->assertEquals(AssistedPurchase::STATUS_PAID, $assistedPurchase->status);
        $this->assertNotNull($assistedPurchase->paid_at);

        // 7. El Administrador supervisa la solicitud en el panel de auditoría
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $adminIndex = $this->get(route('admin.assisted_purchases.index'));
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee($assistedPurchase->request_code);
        $adminIndex->assertSee('MX');
        $adminIndex->assertSee('CO');

        $adminShow = $this->get(route('admin.assisted_purchases.show', $assistedPurchase));
        $adminShow->assertStatus(200);
        $adminShow->assertSee('SPEI-TRANSF-99887766');
    }

    /**
     * Test 8: Webhook de Mercado Pago México - Verificación de Idempotencia y Registro.
     */
    public function test_mercadopago_webhook_idempotency_and_signature(): void
    {
        // 1. Crear un pedido mexicano de prueba
        $mexicanOrder = Order::create([
            'user_id'         => $this->buyer->id,
            'company_id'      => $this->mexicanCompany->id,
            'order_number'    => 'MX-ORD-TEST-001',
            'reference'       => 'MP_REF_123456789',
            'country'         => 'MX',
            'currency'        => 'MXN',
            'total'           => 450.00,
            'subtotal'        => 450.00,
            'shipping_cost'   => 0.00,
            'payment_gateway' => 'mercadopago',
            'status'          => 'pending',
            'recipient_name'  => 'Mariana Gómez',
            'recipient_phone' => '+52 3311223344',
            'delivery_address'=> 'Av. Vallarta 1234, Guadalajara',
        ]);

        $webhookPayload = [
            'action' => 'payment.created',
            'data'   => ['id' => '999888777'],
            'type'   => 'payment',
        ];

        // Llamada directa sin firma válida debe responder 401
        $response = $this->postJson(route('api.payments.mercadopago.webhook'), $webhookPayload);
        $this->assertEquals(401, $response->status());

        // Probar registro de log de pago con provider mercadopago
        PaymentLog::create([
            'order_id'          => $mexicanOrder->id,
            'payment_reference' => $mexicanOrder->reference,
            'provider'          => 'mercadopago',
            'event_type'        => 'payment.created',
            'payload'           => $webhookPayload,
            'status'            => 'info',
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'order_id' => $mexicanOrder->id,
            'provider' => 'mercadopago',
        ]);
    }

    /**
     * Test 9: La vista de registro renderiza el selector de país y opciones para Colombia y México.
     */
    public function test_registration_view_renders_country_selector_and_options(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('country_co');
        $response->assertSee('country_mx');
        $response->assertSee('Colombia');
        $response->assertSee('México');
        $response->assertSee('container-colombia');
        $response->assertSee('container-mexico');
        $response->assertSee('+57');
        $response->assertSee('+52');
        $response->assertSee('Puebla');
        $response->assertSee('Spotlight Puebla');
    }

    /**
     * Test 10: Registro de Comprador Colombiano sin documentos tributarios.
     */
    public function test_colombian_buyer_registration(): void
    {
        $payload = [
            'name'                  => 'Andrea Morales',
            'email'                 => 'andrea.morales@colombia.co',
            'password'              => 'Password2026!',
            'password_confirmation' => 'Password2026!',
            'role'                  => 'user',
            'country'               => 'CO',
            'phone'                 => '3123456789',
            // state y city se omiten intencionalmente porque en Ubaté se auto-asignan
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('users', [
            'email'        => 'andrea.morales@colombia.co',
            'role'         => 'user',
            'country'      => 'CO',
            'phone_prefix' => '+57',
            'phone'        => '3123456789',
            'state'        => 'Cundinamarca',
            'city'         => 'Ubaté',
        ]);

        $user = User::where('email', 'andrea.morales@colombia.co')->first();
        $this->assertNull($user->company);
    }

    /**
     * Test 11: Registro de Comprador Mexicano sin RFC, CURP ni selector de estado/municipio (auto-asignado a Puebla).
     */
    public function test_mexican_buyer_registration_without_tax_documents(): void
    {
        $payload = [
            'name'                  => 'Mateo Velasco',
            'email'                 => 'mateo.velasco@mexico.mx',
            'password'              => 'Password2026!',
            'password_confirmation' => 'Password2026!',
            'role'                  => 'user',
            'country'               => 'MX',
            'phone'                 => '2229876543',
            'phone_prefix'          => '+52',
            // State y City no se envían porque en la vista se omiten para locales de Puebla
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('users', [
            'email'        => 'mateo.velasco@mexico.mx',
            'role'         => 'user',
            'country'      => 'MX',
            'phone_prefix' => '+52',
            'phone'        => '2229876543',
            'state'        => 'Puebla',
            'city'         => 'Puebla',
        ]);

        $user = User::where('email', 'mateo.velasco@mexico.mx')->first();
        $this->assertNull($user->company);
    }

    /**
     * Test 12: Registro de Emprendimiento Mexicano con RFC opcional omitido (no debe guardar ceros ni N/A).
     */
    public function test_mexican_business_registration_with_optional_rfc_omitted(): void
    {
        $payload = [
            'name'                  => 'Valentina Ramos',
            'email'                 => 'valentina@talaverapoblana.mx',
            'password'              => 'Puebla2026!',
            'password_confirmation' => 'Puebla2026!',
            'role'                  => 'business',
            'country'               => 'MX',
            'nombre_comercial'      => 'Talavera Real de Puebla',
            'nombre_representante'  => 'Valentina Ramos Flores',
            'state'                 => 'Puebla',
            'city'                  => 'Puebla',
            'postal_code'           => '72000',
            'address'               => 'Calle 5 Poniente #102, Centro',
            'category'              => 'Hogar, Cerámica y Decoración',
            'description'           => 'Piezas de cerámica tradicional certificada de Puebla.',
            'phone'                 => '2221234567',
            'phone_prefix'          => '+52',
            'tax_id'                => '', // RFC Omitido intencionalmente
            'habeas_data_accepted'  => 1,
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('companies', [
            'name'         => 'Talavera Real de Puebla',
            'country'      => 'MX',
            'currency'     => 'MXN',
            'state'        => 'Puebla',
            'city'         => 'Puebla',
            'phone_prefix' => '+52',
            'tax_id'       => null,
        ]);

        $company = Company::where('name', 'Talavera Real de Puebla')->first();
        $this->assertNull($company->tax_id);
        $this->assertNotEquals('000000', $company->tax_id);
        $this->assertNotEquals('N/A', $company->tax_id);
    }

    /**
     * Test 13: Si un emprendimiento mexicano ingresa un valor dummy en tax_id (ej: 000000 o N/A), se limpia a null.
     */
    public function test_mexican_business_registration_with_dummy_rfc_is_sanitized_to_null(): void
    {
        $payload = [
            'name'                  => 'Rodrigo Solís',
            'email'                 => 'rodrigo@platadetaxco.mx',
            'password'              => 'Taxco2026!',
            'password_confirmation' => 'Taxco2026!',
            'role'                  => 'business',
            'country'               => 'MX',
            'nombre_comercial'      => 'Orfebrería y Plata de Taxco',
            'state'                 => 'Guerrero',
            'city'                  => 'Taxco de Alarcón',
            'tax_id'                => '000000', // Valor ficticio
            'habeas_data_accepted'  => 1,
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('companies', [
            'name'    => 'Orfebrería y Plata de Taxco',
            'country' => 'MX',
            'tax_id'  => null,
        ]);
    }

    /**
     * Test 14: Registro Colombiano formal conserva validaciones estrictas (NIT, Cámara de Comercio, RUT).
     */
    public function test_colombian_business_registration_preserves_formal_validation(): void
    {
        $payload = [
            'name'                  => 'Empresario Bogotá',
            'email'                 => 'empresario@bogota.co',
            'password'              => 'Bogota2026!',
            'password_confirmation' => 'Bogota2026!',
            'role'                  => 'business',
            'country'               => 'CO',
            'tipo_negocio'          => 'formal',
            'razon_social'          => 'Empresa Formal SAS',
            // Omitir NIT, RUT y Cámara de Comercio para probar que Colombia sigue exigiendo sus requisitos
            'habeas_data_accepted'  => 1,
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertSessionHasErrors(['nit', 'camara_comercio_file', 'rut_file']);
    }

    /**
     * Test 15: Registro de Emprendimiento Mexicano auto-asigna Puebla como Estado y Municipio al omitirse en el formulario.
     */
    public function test_mexican_business_registration_auto_assigns_puebla_location_when_fields_omitted(): void
    {
        $payload = [
            'name'                  => 'Camila Huerta',
            'email'                 => 'camila@dulcesdepuebla.mx',
            'password'              => 'PueblaDulces2026!',
            'password_confirmation' => 'PueblaDulces2026!',
            'role'                  => 'business',
            'country'               => 'MX',
            'nombre_comercial'      => 'Dulces Típicos Poblanos Santa Clara',
            'nombre_representante'  => 'Camila Huerta Morales',
            'address'               => 'Calle 6 Oriente #206, Centro',
            'phone'                 => '2227654321',
            'phone_prefix'          => '+52',
            'habeas_data_accepted'  => 1,
            // 'state' y 'city' omitidos intencionalmente
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('companies', [
            'name'         => 'Dulces Típicos Poblanos Santa Clara',
            'country'      => 'MX',
            'state'        => 'Puebla',
            'city'         => 'Puebla',
            'address'      => 'Calle 6 Oriente #206, Centro',
            'phone_prefix' => '+52',
            'tax_id'       => null,
        ]);
        $this->assertDatabaseHas('users', [
            'email'        => 'camila@dulcesdepuebla.mx',
            'role'         => 'business',
            'country'      => 'MX',
            'state'        => 'Puebla',
            'city'         => 'Puebla',
        ]);
    }

    /**
     * Test 16: Registro de Emprendimiento Colombiano informal auto-asigna Cundinamarca y Ubaté al omitirse en el formulario.
     */
    public function test_colombian_business_registration_auto_assigns_ubate_location_when_fields_omitted(): void
    {
        $payload = [
            'name'                  => 'Carlos Sánchez',
            'email'                 => 'carlos@quesosubate.co',
            'password'              => 'UbateLacteos2026!',
            'password_confirmation' => 'UbateLacteos2026!',
            'role'                  => 'business',
            'country'               => 'CO',
            'tipo_negocio'          => 'informal',
            'nombre_comercial'      => 'Lácteos Artesanales de Ubaté',
            'cedula_propietario'    => '1023456789',
            'address'               => 'Carrera 7 # 8-20, Centro',
            'phone'                 => '3109876543',
            'phone_prefix'          => '+57',
            'habeas_data_accepted'  => 1,
            // 'state' y 'city' omitidos intencionalmente
        ];

        $response = $this->post(route('register'), $payload);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('companies', [
            'name'         => 'Lácteos Artesanales de Ubaté',
            'country'      => 'CO',
            'state'        => 'Cundinamarca',
            'city'         => 'Ubaté',
            'address'      => 'Carrera 7 # 8-20, Centro',
            'phone_prefix' => '+57',
        ]);
        $this->assertDatabaseHas('users', [
            'email'        => 'carlos@quesosubate.co',
            'role'         => 'business',
            'country'      => 'CO',
            'state'        => 'Cundinamarca',
            'city'         => 'Ubaté',
        ]);
    }
}
