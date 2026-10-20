<?php

namespace Tests\Feature;

use App\Models\BillingConfiguration;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Services\Billing\ElectronicDocumentService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElectronicBillingTest extends TestCase
{
    use RefreshDatabase;

    private function createMerchant(string $tipoNegocio = 'formal', string $fiscalStatus = CompanyFiscalProfile::STATUS_ENABLED): array
    {
        $user = User::factory()->create([
            'role' => 'business',
            'email' => 'merchant_' . uniqid() . '@spotlight.local',
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Empresa ' . uniqid(),
            'tipo_negocio' => $tipoNegocio,
            'razon_social' => 'Empresa SAS ' . uniqid(),
            'nit' => '901234567-1',
            'status' => 'active',
            'address' => 'Carrera 7 # 8-45, Ubaté',
            'phone' => '3101234567',
            'email' => 'empresa@spotlight.local',
        ]);

        $profile = CompanyFiscalProfile::create([
            'company_id' => $company->id,
            'fiscal_status' => $fiscalStatus,
            'person_type' => 'persona_juridica',
            'id_type' => '31',
            'identification_number' => '901234567',
            'dv' => '1',
            'business_name' => $company->razon_social,
            'commercial_name' => $company->name,
            'tax_regime' => 'responsable_iva',
            'tax_responsibilities' => ['O-13', 'O-47'],
            'ciiu_code' => '4719',
            'fiscal_address' => $company->address,
            'municipality_code' => '25843',
            'department_code' => '25',
            'fiscal_email' => $company->email,
            'fiscal_phone' => $company->phone,
        ]);

        $config = BillingConfiguration::create([
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

        return [$user, $company, $profile, $config];
    }

    /**
     * 1. Un comercio informal no puede emitir facturas electrónicas.
     */
    public function test_informal_merchant_cannot_issue_electronic_invoices(): void
    {
        [$user, $company] = $this->createMerchant('informal', CompanyFiscalProfile::STATUS_NOT_REQUESTED);

        $this->assertFalse($company->canIssueElectronicInvoices());

        // Ingresar al panel de facturación muestra el aviso educativo de formalización
        $indexResponse = $this->actingAs($user)->get(route('business.billing.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Módulo de Facturación Electrónica DIAN');
        $indexResponse->assertSee('Comercio Informal / Emprendimiento');

        // Intentar ingresar directamente a la pantalla de emisión de ventas redirige
        $salesResponse = $this->actingAs($user)->get(route('business.billing.sales'));
        $salesResponse->assertRedirect(route('business.billing.index'));

        // Intentar llamar directamente al servicio debe lanzar excepción de dominio
        $this->expectException(DomainException::class);
        $service = app(ElectronicDocumentService::class);
        $order = Order::create(['user_id' => $user->id, 'company_id' => $company->id, 'total' => 50000, 'status' => 'paid']);
        $service->createInvoiceFromOrder($company, $order, ['name' => 'Cliente']);
    }

    /**
     * 2. Una empresa formal sin verificar tampoco puede emitir facturación electrónica.
     */
    public function test_unverified_formal_company_cannot_issue_invoices(): void
    {
        [$user, $company] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_IN_REVIEW);

        $this->assertFalse($company->canIssueElectronicInvoices());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('El comercio no está legalmente verificado ni habilitado');

        $service = app(ElectronicDocumentService::class);
        $order = Order::create(['user_id' => $user->id, 'company_id' => $company->id, 'total' => 50000, 'status' => 'paid']);
        $service->createInvoiceFromOrder($company, $order, ['name' => 'Cliente']);
    }

    /**
     * 3. La verificación legal se separa de la habilitación técnica.
     */
    public function test_legal_verification_is_separated_from_technical_setup(): void
    {
        [$user, $company, $profile, $config] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_VERIFIED);
        $config->update(['is_connected' => false]);

        // Verificado legalmente es true
        $this->assertTrue($profile->isVerified());
        // Pero no está habilitado para emitir mientras su estado fiscal no sea 'enabled'
        $this->assertFalse($company->canIssueElectronicInvoices());
    }

    /**
     * 4. Aislamiento y Políticas: Un comercio no puede acceder a documentos de otra empresa.
     */
    public function test_company_cannot_access_or_download_documents_of_another_company(): void
    {
        [$userA, $companyA] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);
        [$userB, $companyB] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $documentA = ElectronicDocument::create([
            'company_id' => $companyA->id,
            'document_type' => '01',
            'prefix' => 'SETP',
            'consecutive' => 1,
            'full_number' => 'SETP-1',
            'issue_date' => now(),
            'issue_time' => '12:00:00',
            'issuer_nit' => '901234567',
            'issuer_business_name' => 'Empresa A',
            'customer_name' => 'Cliente',
            'line_extension_amount' => 10000,
            'tax_exclusive_amount' => 10000,
            'tax_inclusive_amount' => 11900,
            'tax_total_amount' => 1900,
            'payable_amount' => 11900,
            'status' => 'validated',
        ]);

        // Usuario B intenta ver documento de Empresa A -> 403 Forbidden
        $response = $this->actingAs($userB)->get(route('business.billing.documents.show', $documentA));
        $response->assertStatus(403);

        // Usuario B intenta descargar XML de Empresa A -> 403 Forbidden
        $responseDownload = $this->actingAs($userB)->get(route('business.billing.documents.download_xml', $documentA));
        $responseDownload->assertStatus(403);
    }

    /**
     * 5. Carrito multi-vendedor separa los pedidos por vendedor y conserva snapshots históricos.
     */
    public function test_multi_vendor_cart_splits_orders_and_preserves_historical_snapshots(): void
    {
        [$userSellerA, $companyA] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);
        [$userSellerB, $companyB] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $customer = User::factory()->create(['role' => 'user']);

        $productA = Product::create([
            'company_id' => $companyA->id,
            'name' => 'Queso Pera de Ubaté 500g',
            'sku' => 'UBT-QP-500',
            'price' => 15000,
            'stock' => 50,
            'tax_rate' => 19.00,
            'tax_type' => '01',
        ]);

        $productB = Product::create([
            'company_id' => $companyB->id,
            'name' => 'Yogurt Artesanal Melocotón 1L',
            'sku' => 'UBT-YG-1L',
            'price' => 12000,
            'stock' => 30,
            'tax_rate' => 5.00,
            'tax_type' => '01',
        ]);

        // Simular carrito en sesión con productos de ambos comercios
        $cart = [
            $productA->id => 2, // 30.000 COP
            $productB->id => 1, // 12.000 COP
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('user.cart.payment.process'), [
                'card_name' => 'Comprador Prueba',
                'card_number' => '4111111111111111',
                'exp_date' => '12/28',
                'cvv' => '123',
                'address' => 'Barrio San José, Ubaté',
                'payment_method_code' => '48',
            ]);

        $response->assertRedirect(route('user.orders.index'));

        // Se deben haber creado 2 pedidos separados: uno para Company A y otro para Company B
        $this->assertEquals(2, Order::where('user_id', $customer->id)->count());

        $orderA = Order::where('user_id', $customer->id)->where('company_id', $companyA->id)->first();
        $this->assertNotNull($orderA);
        $this->assertEquals(30000, (float) $orderA->total);

        $detailA = $orderA->details->first();
        $this->assertEquals('Queso Pera de Ubaté 500g', $detailA->product_name);
        $this->assertEquals('UBT-QP-500', $detailA->product_sku);
        $this->assertEquals(19.00, (float) $detailA->tax_rate);
        $this->assertEquals(5700, (float) $detailA->tax_amount); // 30.000 * 19%

        $orderB = Order::where('user_id', $customer->id)->where('company_id', $companyB->id)->first();
        $this->assertNotNull($orderB);
        $this->assertEquals(12000, (float) $orderB->total);
    }

    /**
     * 6. Consistencia de cálculos históricos y protección contra borrado accidental de productos.
     */
    public function test_product_deletion_is_blocked_when_order_history_exists(): void
    {
        [$userSeller, $company] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Arequipe Tradicional 250g',
            'price' => 8000,
            'stock' => 20,
        ]);

        $order = Order::create(['user_id' => $userSeller->id, 'company_id' => $company->id, 'total' => 8000, 'status' => 'paid']);
        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'company_id' => $company->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 8000,
            'subtotal' => 8000,
        ]);

        // Intentar eliminar el producto por el comerciante
        $response = $this->actingAs($userSeller)->delete(route('business.products.destroy', $product));
        // Debe ser rechazado para proteger la trazabilidad contable
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * 7. Emisión exitosa de factura electrónica con cálculo de CUFE SHA-384 y generación UBL 2.1.
     */
    public function test_invoice_emission_calculates_cufe_sha384_and_generates_ubl_xml(): void
    {
        [$user, $company, $profile, $config] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Mantequilla de Campo 250g',
            'sku' => 'UBT-MNT-250',
            'price' => 10000,
            'stock' => 15,
            'tax_rate' => 19.00,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'total' => 10000,
            'status' => 'paid',
            'billing_status' => 'not_billed',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'company_id' => $company->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
            'tax_rate' => 19.00,
            'tax_amount' => 3800,
        ]);

        $service = app(ElectronicDocumentService::class);
        $document = $service->createInvoiceFromOrder(
            $company,
            $order,
            [
                'name' => 'Carlos Rodriguez',
                'id_type' => '13',
                'id_number' => '79845123',
                'email' => 'carlos@ejemplo.com',
                'address' => 'Calle 8 # 5-10, Ubaté',
            ],
            '10'
        );

        $this->assertEquals(ElectronicDocument::STATUS_VALIDATED, $document->status);
        $this->assertEquals('SETP-100', $document->full_number);
        $this->assertNotNull($document->uuid_cufe);
        $this->assertEquals(96, strlen($document->uuid_cufe)); // SHA-384 produce 96 caracteres hexadecimales
        $this->assertStringContainsString('catalogo-vpfe.dian.gov.co', $document->qr_content);
        $this->assertEquals('billed', $order->fresh()->billing_status);

        // Verificar consecutivo incrementado
        $this->assertEquals(101, $config->fresh()->current_consecutive);
    }

    /**
     * 8. Emisión de Nota Crédito vinculada a la factura previa.
     */
    public function test_credit_note_emission_and_parent_invoice_reference(): void
    {
        [$user, $company, $profile, $config] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $service = app(ElectronicDocumentService::class);
        $invoice = $service->createDirectInvoice(
            $company,
            ['name' => 'Cliente A', 'id_type' => '13', 'id_number' => '12345'],
            [
                ['description' => 'Servicio de Consultoría', 'quantity' => 1, 'price_amount' => 100000, 'tax_rate' => 19]
            ]
        );

        $this->assertTrue($invoice->isValidated());

        // Emitir nota crédito de anulación total (Concepto 2)
        $creditNote = $service->createCreditNote($invoice, '2', 'Anulación por error en digitación');

        $this->assertEquals(ElectronicDocument::TYPE_CREDIT_NOTE, $creditNote->document_type);
        $this->assertEquals(ElectronicDocument::STATUS_VALIDATED, $creditNote->status);
        $this->assertEquals($invoice->id, $creditNote->parent_document_id);
        $this->assertNotNull($creditNote->uuid_cufe); // CUDE

        // Factura original marcada como cancelada
        $this->assertEquals(ElectronicDocument::STATUS_CANCELLED, $invoice->fresh()->status);
    }

    /**
     * 9. Protección contra validaciones falsas: Si el consecutivo está fuera de rango, la DIAN rechaza.
     */
    public function test_http_success_does_not_equate_to_dian_acceptance_if_rejection_rules_fail(): void
    {
        [$user, $company, $profile, $config] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        // Forzar consecutivo fuera del rango autorizado por la DIAN (5001 > 5000)
        $config->update(['current_consecutive' => 5001]);

        $service = app(ElectronicDocumentService::class);
        $document = $service->createDirectInvoice(
            $company,
            ['name' => 'Cliente X', 'id_type' => '13', 'id_number' => '999'],
            [['description' => 'Ítem', 'quantity' => 1, 'price_amount' => 5000, 'tax_rate' => 0]]
        );

        // El documento NO debe ser validado, debe quedar en estado REJECTED
        $this->assertEquals(ElectronicDocument::STATUS_REJECTED, $document->status);
        $this->assertNull($document->uuid_cufe);
        $this->assertStringContainsString('fuera del rango autorizado', $document->dian_response_message);
    }

    /**
     * 10. Dashboard de facturación orientado al empresario con diseño claro y libre de tecnicismos.
     */
    public function test_merchant_billing_dashboard_renders_friendly_badges_and_texts(): void
    {
        [$user, $company, $profile, $config] = $this->createMerchant('formal', CompanyFiscalProfile::STATUS_ENABLED);

        $service = app(ElectronicDocumentService::class);
        $doc = $service->createDirectInvoice(
            $company,
            ['name' => 'Empresa Cliente SAS', 'id_type' => '31', 'id_number' => '900999888-2', 'email' => 'cliente@ejemplo.com'],
            [['description' => 'Servicio de Consultoría', 'quantity' => 1, 'price_amount' => 100000, 'tax_rate' => 19]]
        );

        $response = $this->actingAs($user)->get(route('business.billing.index'));

        $response->assertStatus(200);

        // 1. Badge amigable de servicio conectado (sin "SANDBOX" ni "Modo Demostración")
        $response->assertSee('Servicio DIAN Conectado');
        $response->assertDontSee('Modo Demostración');
        $response->assertDontSee('PROVEEDOR CONECTADO (SANDBOX)');

        // 2. Stepper con nomenclatura de Formulario 1876 y botón Emitir Factura
        $response->assertSee('Emitir Factura');
        $response->assertSee('Resolución y Rangos DIAN (Formulario 1876)');
        $response->assertSee('Habilitado para Facturar');

        // 3. Menú lateral sin referencias técnicas a APIs
        $response->assertSee('Resolución de Facturación');
        $response->assertSee('Configuración de rangos y prefijos (Form. 1876)');
        $response->assertDontSee('Resolución DIAN & API');
        $response->assertDontSee('ambiente sandbox/producción');

        // 4. Tarjeta minimalista de confianza y soporte
        $response->assertSee('Facturación Certificada');
        $response->assertSee('Tus facturas y notas electrónicas cuentan con validación previa oficial ante la DIAN y código QR de autenticidad.');
        $response->assertSee('¿Tienes dudas con tu resolución? Consulta la guía aquí');
        $response->assertDontSee('Cumplimiento Anexo Técnico 1.9');
        $response->assertDontSee('cálculo criptográfico de CUFE en SHA-384');

        // 5. Tabla con badges de estado y botones de acción rápida
        $response->assertSee($doc->full_number);
        $response->assertSee('Validado DIAN');
        $response->assertSee('Empresa Cliente SAS');
        $response->assertSee('mailto:cliente@ejemplo.com');
    }
}

