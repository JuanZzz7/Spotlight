<?php

namespace Tests\Feature;

use App\Models\{Company, CompanySettlement, Order, OrderDetail, PaymentLog, Product, User};
use App\Services\Payment\WompiGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WompiCheckoutAndSettlementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('wompi.public_key', 'pub_test_testkey123');
        Config::set('wompi.private_key', 'prv_test_testkey456');
        Config::set('wompi.integrity_secret', 'test_integrity_secret_789');
        Config::set('wompi.event_secret', 'test_events_secret_abc');
        Config::set('wompi.platform_fee_percent', 5.0);
        Config::set('wompi.shipping_cost_per_vendor', 6000.0);
        Config::set('wompi.free_shipping_threshold', 100000.0);
    }

    protected function createMerchantWithProduct(string $companyName, float $price, int $stock = 20): array
    {
        $user = User::factory()->create(['role' => 'business']);
        $company = Company::create([
            'user_id'                 => $user->id,
            'name'                    => $companyName,
            'status'                  => 'active',
            'bank_name'               => 'Bancolombia',
            'account_type'            => 'ahorros',
            'account_number'          => '123-456789-01',
            'account_holder_name'     => $companyName,
            'account_holder_doc_type' => 'NIT',
            'account_holder_doc_number' => '901234567-1',
            'wompi_merchant_id'       => 'sub_' . strtolower(substr($companyName, 0, 4)),
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name'       => "Producto {$companyName}",
            'sku'        => 'SKU-' . strtoupper(substr($companyName, 0, 3)),
            'price'      => $price,
            'stock'      => $stock,
            'active'     => true,
            'tax_rate'   => 19.00,
            'tax_type'   => '01',
        ]);

        return [$user, $company, $product];
    }

    /**
     * 1. La pantalla de Checkout carga y muestra productos agrupados por comercio con precarga de usuario.
     */
    public function test_checkout_form_loads_preloaded_user_data_and_vendor_grouped_items(): void
    {
        [$sellerA, $companyA, $productA] = $this->createMerchantWithProduct('Lácteos Ubaté', 25000);
        [$sellerB, $companyB, $productB] = $this->createMerchantWithProduct('Artesanías del Valle', 35000);

        $customer = User::factory()->create([
            'name'  => 'Carlos Ramírez',
            'email' => 'carlos.ramirez@example.com',
            'role'  => 'user',
        ]);

        $cart = [
            $productA->id => 2, // 50.000
            $productB->id => 1, // 35.000
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee('Lácteos Ubaté');
        $response->assertSee('Artesanías del Valle');
        $response->assertSee('carlos.ramirez@example.com');
        $response->assertSee('Finalizar Pedido');
        $response->assertSee('Wompi');
    }

    /**
     * 2. El backend ignora precios alterados del frontend y calcula la firma de integridad sobre precios reales de la BD.
     */
    public function test_backend_recalculates_real_prices_and_generates_exact_sha256_signature(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Dulces Tradicionales', 20000, 10);
        $customer = User::factory()->create(['role' => 'user']);

        $cart = [$product->id => 2]; // 40.000 COP + 6.000 envío = 46.000 COP (4.600.000 centavos)

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->postJson(route('checkout.process'), [
                'first_name'       => 'María',
                'last_name'        => 'López',
                'email'            => 'maria@example.com',
                'phone'            => '3109876543',
                'doc_type'         => 'CC',
                'doc_number'       => '52987654',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Calle 9 # 4-20',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
                // Intento malicioso de inyectar precio falso en el request
                'total_manipulated' => 100,
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertTrue($response->json('success'));
        $this->assertEquals(46000, $data['total_cop']);
        $this->assertEquals(4600000, $data['amount_in_cents']);

        // Verificación de la firma SHA-256 exacta: reference + amountInCents + COP + integritySecret
        $expectedSignature = hash('sha256', $data['payment_reference'] . '4600000' . 'COP' . 'test_integrity_secret_789');
        $this->assertEquals($expectedSignature, $data['integrity_signature']);
        $this->assertEquals($expectedSignature, $data['widget_config']['signature']['integrity']);
    }

    /**
     * 3. Bloqueo de concurrencia y validación estricta de stock disponible.
     */
    public function test_checkout_process_blocks_when_stock_is_insufficient(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Queseras Unidas', 15000, 2);
        $customer = User::factory()->create(['role' => 'user']);

        // Intentar comprar 5 unidades cuando solo hay 2
        $cart = [$product->id => 5];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->postJson(route('checkout.process'), [
                'first_name'       => 'Juan',
                'last_name'        => 'Pérez',
                'email'            => 'juan@example.com',
                'phone'            => '3001234567',
                'doc_type'         => 'CC',
                'doc_number'       => '10123456',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Carrera 5 # 7-12',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('no cuenta con inventario suficiente', $response->json('error'));
    }

    /**
     * 4. Carrito multivendedor crea órdenes separadas con cálculo exacto de dispersión por comercio.
     */
    public function test_multi_vendor_cart_splits_orders_and_stores_settlement_fields(): void
    {
        [$sellerA, $companyA, $productA] = $this->createMerchantWithProduct('Comercio Norte', 40000, 10);
        [$sellerB, $companyB, $productB] = $this->createMerchantWithProduct('Comercio Sur', 50000, 10);

        $customer = User::factory()->create(['role' => 'user']);

        $cart = [
            $productA->id => 1, // 40.000 + 6.000 envío = 46.000
            $productB->id => 1, // 50.000 + 6.000 envío = 56.000
        ];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->postJson(route('checkout.process'), [
                'first_name'       => 'Elena',
                'last_name'        => 'Gómez',
                'email'            => 'elena@example.com',
                'phone'            => '3157894512',
                'doc_type'         => 'CC',
                'doc_number'       => '1032456789',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Barrio Santa Bárbara',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $response->assertStatus(200);
        $paymentRef = $response->json('data.payment_reference');

        // Se deben haber creado 2 órdenes asociadas a la misma payment_reference
        $orders = Order::byPaymentReference($paymentRef)->get();
        $this->assertCount(2, $orders);

        $orderA = $orders->firstWhere('company_id', $companyA->id);
        $this->assertNotNull($orderA);
        $this->assertEquals(40000, (float) $orderA->subtotal_company);
        $this->assertEquals(6000, (float) $orderA->shipping_cost_company);
        $this->assertEquals(2000, (float) $orderA->platform_fee); // 5% de 40.000 = 2.000
        $this->assertEquals(44000, (float) $orderA->net_company_amount); // 40.000 + 6.000 - 2.000 = 44.000
        $this->assertEquals(Order::SETTLEMENT_PENDING, $orderA->settlement_status);

        $orderB = $orders->firstWhere('company_id', $companyB->id);
        $this->assertNotNull($orderB);
        $this->assertEquals(50000, (float) $orderB->subtotal_company);
        $this->assertEquals(6000, (float) $orderB->shipping_cost_company);
        $this->assertEquals(2500, (float) $orderB->platform_fee); // 5% de 50.000 = 2.500
        $this->assertEquals(53500, (float) $orderB->net_company_amount); // 50.000 + 6.000 - 2.500 = 53.500

        // El stock debió descontarse
        $this->assertEquals(9, $productA->fresh()->stock);
        $this->assertEquals(9, $productB->fresh()->stock);
    }

    /**
     * 5. Webhook rechaza solicitudes con firma criptográfica checksum incorrecta.
     */
    public function test_wompi_webhook_rejects_invalid_cryptographic_signature(): void
    {
        $payload = [
            'event' => 'transaction.updated',
            'data' => [
                'transaction' => [
                    'id'               => 'tx_test_123',
                    'reference'        => 'SPOT-PAY-TEST',
                    'amount_in_cents'  => 5000000,
                    'status'           => 'APPROVED',
                ]
            ],
            'timestamp' => 1730000000,
            'signature' => [
                'properties' => ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'],
                'checksum'   => 'checksum_falso_invalido_123456',
            ]
        ];

        $response = $this->postJson(route('api.payments.wompi.webhook'), $payload);
        $response->assertStatus(401);
        $response->assertJson(['code' => 'INVALID_CHECKSUM']);
    }

    /**
     * 6. Webhook con firma auténtica de pago APROBADO transiciona pedidos y liquida a los comercios.
     */
    public function test_wompi_webhook_approved_transitions_orders_and_triggers_settlement(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Lácteos El Carmen', 50000);
        $customer = User::factory()->create(['role' => 'user']);

        $paymentRef = 'SPOT-PAY-REAL-123';
        $order = Order::create([
            'user_id'                  => $customer->id,
            'company_id'               => $company->id,
            'reference'                => 'SPOT-ORD-1',
            'payment_reference'        => $paymentRef,
            'total'                    => 56000, // 50.000 + 6.000
            'subtotal_company'         => 50000,
            'shipping_cost_company'    => 6000,
            'platform_fee'             => 2500,
            'net_company_amount'       => 53500,
            'status'                   => 'pending',
            'order_status'             => Order::ORDER_PENDING_PAYMENT,
            'payment_status'           => Order::PAYMENT_PENDING,
            'settlement_status'        => Order::SETTLEMENT_PENDING,
        ]);

        $txId = '12345-67890-test-tx';
        $amountInCents = 5600000;
        $status = 'APPROVED';
        $timestamp = 1730000100;

        // Construcción de la firma auténtica según especificación Wompi
        $eventSecret = 'test_events_secret_abc';
        $concatenated = $txId . $status . $amountInCents . $timestamp . $eventSecret;
        $checksum = hash('sha256', $concatenated);

        $payload = [
            'event' => 'transaction.updated',
            'data' => [
                'transaction' => [
                    'id'                  => $txId,
                    'reference'           => $paymentRef,
                    'amount_in_cents'     => $amountInCents,
                    'status'              => $status,
                    'currency'            => 'COP',
                    'payment_method_type' => 'PSE',
                ]
            ],
            'timestamp' => $timestamp,
            'signature' => [
                'properties' => ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'],
                'checksum'   => $checksum,
            ]
        ];

        $response = $this->postJson(route('api.payments.wompi.webhook'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verificar actualización en la base de datos
        $order->refresh();
        $this->assertEquals(Order::PAYMENT_APPROVED, $order->payment_status);
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(Order::ORDER_IN_PREPARATION, $order->order_status);
        $this->assertEquals($txId, $order->transaction_id);
        $this->assertEquals('PSE', $order->payment_method);
        $this->assertEquals('31', $order->payment_method_code);
        $this->assertEquals(Order::SETTLEMENT_SETTLED, $order->settlement_status);

        // Verificar creación de registro en company_settlements
        $settlement = CompanySettlement::where('order_id', $order->id)->first();
        $this->assertNotNull($settlement);
        $this->assertEquals($company->id, $settlement->company_id);
        $this->assertEquals(53500, (float) $settlement->net_payout);
        $this->assertEquals(CompanySettlement::STATUS_SETTLED, $settlement->status);
        $this->assertEquals($txId, $settlement->wompi_transaction_id);

        // Verificar log de auditoría
        $log = PaymentLog::where('payment_reference', $paymentRef)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('payment_approved', $log->status);
    }

    /**
     * 7. Webhook con pago RECHAZADO cancela órdenes y restaura el inventario automáticamente.
     */
    public function test_wompi_webhook_declined_restores_inventory_and_cancels_order(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Artesanías Andinas', 30000, 8);
        $customer = User::factory()->create(['role' => 'user']);

        $paymentRef = 'SPOT-PAY-DECLINE-123';
        $order = Order::create([
            'user_id'                  => $customer->id,
            'company_id'               => $company->id,
            'reference'                => 'SPOT-ORD-DEC-1',
            'payment_reference'        => $paymentRef,
            'total'                    => 36000,
            'subtotal_company'         => 30000,
            'shipping_cost_company'    => 6000,
            'platform_fee'             => 1500,
            'net_company_amount'       => 34500,
            'status'                   => 'pending',
            'order_status'             => Order::ORDER_PENDING_PAYMENT,
            'payment_status'           => Order::PAYMENT_PENDING,
        ]);

        OrderDetail::create([
            'order_id'        => $order->id,
            'product_id'      => $product->id,
            'company_id'      => $company->id,
            'product_name'    => $product->name,
            'quantity'        => 2,
            'unit_price'      => $product->price,
            'subtotal'        => 30000,
            'tax_rate'        => 19.00,
            'tax_amount'      => 5700,
            'discount_amount' => 0.00,
        ]);

        $txId = 'tx_declined_999';
        $amountInCents = 3600000;
        $status = 'DECLINED';
        $timestamp = 1730000200;

        $concatenated = $txId . $status . $amountInCents . $timestamp . 'test_events_secret_abc';
        $checksum = hash('sha256', $concatenated);

        $payload = [
            'event' => 'transaction.updated',
            'data' => [
                'transaction' => [
                    'id'                  => $txId,
                    'reference'           => $paymentRef,
                    'amount_in_cents'     => $amountInCents,
                    'status'              => $status,
                    'payment_method_type' => 'CARD',
                ]
            ],
            'timestamp' => $timestamp,
            'signature' => [
                'properties' => ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'],
                'checksum'   => $checksum,
            ]
        ];

        $response = $this->postJson(route('api.payments.wompi.webhook'), $payload);
        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_DECLINED, $order->payment_status);
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals(Order::ORDER_CANCELLED, $order->order_status);

        // El inventario debió restaurarse de 8 a 10
        $this->assertEquals(10, $product->fresh()->stock);
    }

    /**
     * 8. Idempotencia del webhook: No duplica operaciones si el evento ya fue procesado.
     */
    public function test_wompi_webhook_is_idempotent(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Queso Pera Gourmet', 40000);
        $customer = User::factory()->create(['role' => 'user']);

        $txId = 'tx_idempotent_1';
        $paymentRef = 'SPOT-PAY-IDEMP-01';

        $order = Order::create([
            'user_id'                  => $customer->id,
            'company_id'               => $company->id,
            'reference'                => 'SPOT-ORD-IDEMP',
            'payment_reference'        => $paymentRef,
            'transaction_id'           => $txId,
            'total'                    => 46000,
            'status'                   => 'paid',
            'order_status'             => Order::ORDER_CONFIRMED,
            'payment_status'           => Order::PAYMENT_APPROVED,
        ]);

        $amountInCents = 4600000;
        $status = 'APPROVED';
        $timestamp = 1730000300;

        $concatenated = $txId . $status . $amountInCents . $timestamp . 'test_events_secret_abc';
        $checksum = hash('sha256', $concatenated);

        $payload = [
            'event' => 'transaction.updated',
            'data' => [
                'transaction' => [
                    'id'                  => $txId,
                    'reference'           => $paymentRef,
                    'amount_in_cents'     => $amountInCents,
                    'status'              => $status,
                ]
            ],
            'timestamp' => $timestamp,
            'signature' => [
                'properties' => ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'],
                'checksum'   => $checksum,
            ]
        ];

        // Se envía el mismo webhook
        $response = $this->postJson(route('api.payments.wompi.webhook'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'already_processed']);
    }

    /**
     * 9. Una empresa puede configurar sus datos bancarios y su subcuenta de Wompi.
     */
    public function test_company_can_update_banking_and_wompi_details(): void
    {
        [$user, $company] = $this->createMerchantWithProduct('Comercio Config', 10000);

        $response = $this->actingAs($user)->post(route('business.company.update'), [
            'section'                   => 'banking',
            'bank_name'                 => 'Davivienda',
            'account_type'              => 'corriente',
            'account_number'            => '987-654321-00',
            'account_holder_name'       => 'Comercio Config SAS',
            'account_holder_doc_type'   => 'NIT',
            'account_holder_doc_number' => '900888777-1',
            'wompi_merchant_id'         => 'sub_wompi_777',
            'split_rule_id'             => 'rule_split_01',
        ]);

        $response->assertRedirect(route('business.company.edit'));
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertEquals('Davivienda', $company->bank_name);
        $this->assertEquals('corriente', $company->account_type);
        $this->assertEquals('987-654321-00', $company->account_number);
        $this->assertEquals('Comercio Config SAS', $company->account_holder_name);
        $this->assertEquals('sub_wompi_777', $company->wompi_merchant_id);
    }

    /**
     * 10. Pago con Tarjeta registra el titular, máscara de tarjeta, aprueba la orden y dispersa liquidación.
     */
    public function test_checkout_with_credit_card_records_cardholder_and_approves_order(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Tejidos Boyacá', 40000, 5);
        $customer = User::factory()->create(['role' => 'user']);

        $cart = [$product->id => 1];

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'payment_method'   => 'card',
                'first_name'       => 'Pedro',
                'last_name'        => 'Gómez',
                'email'            => 'pedro@example.com',
                'phone'            => '3157778899',
                'doc_type'         => 'CC',
                'doc_number'       => '1023456789',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Carrera 6 # 10-25',
                'card_name'        => 'PEDRO GOMEZ',
                'card_number'      => '4532 1122 3344 5566',
                'exp_date'         => '12/28',
                'cvv'              => '789',
                'installments'     => 1,
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(Order::PAYMENT_APPROVED, $order->payment_status);
        $this->assertEquals(Order::ORDER_CONFIRMED, $order->order_status);
        $this->assertStringContainsString('5566', $order->payment_method);
        $this->assertStringContainsString('PEDRO GOMEZ', $order->payment_method);

        // Verifica que la liquidación se haya generado
        $settlement = CompanySettlement::where('order_id', $order->id)->first();
        $this->assertNotNull($settlement);
        $this->assertEquals('settled', $settlement->status);

        $response->assertRedirect(route('checkout.result', ['reference' => $order->payment_reference]));
    }

    /**
     * 11. Pago con PSE redirige al portal de autorización bancaria y confirma el débito.
     */
    public function test_checkout_with_pse_redirects_to_bank_and_approves_on_authorization(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Miel Pura Ubaté', 25000, 10);
        $customer = User::factory()->create(['role' => 'user']);

        $cart = [$product->id => 2];

        // 1. Submit inicial con PSE
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'payment_method'   => 'pse',
                'first_name'       => 'Ana',
                'last_name'        => 'Herrera',
                'email'            => 'ana@example.com',
                'phone'            => '3114445566',
                'doc_type'         => 'CC',
                'doc_number'       => '52123456',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Calle 4 # 8-30',
                'pse_bank'         => 'Bancolombia',
                'pse_person_type'  => 'natural',
                'pse_email'        => 'ana.pse@example.com',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(Order::PAYMENT_PENDING, $order->payment_status);
        $this->assertStringContainsString('PSE - Bancolombia', $order->payment_method);

        $response->assertRedirect(route('checkout.pse.authorize', ['reference' => $order->payment_reference]));

        // 2. Visita la pantalla de autorización del banco
        $pseViewResponse = $this->actingAs($customer)->get(route('checkout.pse.authorize', ['reference' => $order->payment_reference]));
        $pseViewResponse->assertStatus(200);
        $pseViewResponse->assertSee('Portal Bancario PSE');
        $pseViewResponse->assertSee('Bancolombia');

        // 3. Usuario autoriza el débito en la aplicación bancaria
        $approveResponse = $this->actingAs($customer)->post(route('checkout.pse.approve', ['reference' => $order->payment_reference]));
        $approveResponse->assertRedirect(route('checkout.result', ['reference' => $order->payment_reference]));

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(Order::PAYMENT_APPROVED, $order->payment_status);
        $this->assertEquals(Order::ORDER_CONFIRMED, $order->order_status);
    }

    /**
     * 12. Pago con Código QR genera vista dinámica por el monto exacto y confirma la transacción.
     */
    public function test_checkout_with_qr_generates_qr_view_and_confirms_payment(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Café Artesanal', 30000, 10);
        $customer = User::factory()->create(['role' => 'user']);

        $cart = [$product->id => 1];

        // 1. Submit inicial con QR
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'payment_method'   => 'qr',
                'first_name'       => 'Lucía',
                'last_name'        => 'Suárez',
                'email'            => 'lucia@example.com',
                'phone'            => '3201112233',
                'doc_type'         => 'CC',
                'doc_number'       => '1098765432',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Avenida Central # 1-50',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(Order::PAYMENT_PENDING, $order->payment_status);

        $response->assertRedirect(route('checkout.qr.show', ['reference' => $order->payment_reference]));

        // 2. Visita la pantalla del código QR
        $qrViewResponse = $this->actingAs($customer)->get(route('checkout.qr.show', ['reference' => $order->payment_reference]));
        $qrViewResponse->assertStatus(200);
        $qrViewResponse->assertSee('Escanea para Pagar');
        $qrViewResponse->assertSee('Bancolombia');

        // 3. Confirmar pago escaneado desde la app
        $confirmResponse = $this->actingAs($customer)->post(route('checkout.qr.confirm', ['reference' => $order->payment_reference]));
        $confirmResponse->assertRedirect(route('checkout.result', ['reference' => $order->payment_reference]));

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(Order::PAYMENT_APPROVED, $order->payment_status);
        $this->assertEquals(Order::ORDER_CONFIRMED, $order->order_status);
    }

    /**
     * 13. Intento de compra con tarjeta no verificada o rechazada por el banco emisor es bloqueado.
     */
    public function test_checkout_with_declined_card_is_rejected_and_orders_not_created(): void
    {
        [$seller, $company, $product] = $this->createMerchantWithProduct('Joyería Andina', 50000, 5);
        $customer = User::factory()->create(['role' => 'user']);

        $cart = [$product->id => 1];

        // Tarjeta con terminación 0000 (simula tarjeta denegada o sin fondos)
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'payment_method'   => 'card',
                'first_name'       => 'Carlos',
                'last_name'        => 'Mora',
                'email'            => 'carlos.mora@example.com',
                'phone'            => '3105556677',
                'doc_type'         => 'CC',
                'doc_number'       => '80123456',
                'country'          => 'Colombia',
                'department'       => 'Cundinamarca',
                'city'             => 'Ubaté',
                'address'          => 'Calle 8 # 2-10',
                'card_name'        => 'CARLOS MORA',
                'card_number'      => '4532 1122 3344 0000',
                'exp_date'         => '12/28',
                'cvv'              => '123',
                'terms_accepted'   => '1',
                'privacy_accepted' => '1',
            ]);

        $response->assertSessionHas('error');
        $this->assertNull(Order::where('user_id', $customer->id)->first());

        // El stock permanece intacto (no se descontó)
        $this->assertEquals(5, $product->fresh()->stock);
    }
}
