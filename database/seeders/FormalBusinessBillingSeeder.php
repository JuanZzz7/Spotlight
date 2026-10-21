<?php

namespace Database\Seeders;

use App\Models\BillingConfiguration;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Services\Billing\ElectronicDocumentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FormalBusinessBillingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener o crear Administrador para firmas/aprobaciones
        $admin = User::firstOrCreate(
            ['email' => 'admin@spotlight.com'],
            [
                'name' => 'Administrador Spotlight',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. CONFIGURAR USUARIO EMPRESA FORMAL HABILITADA (Carlos - TechStore Pro)
        $carlos = User::firstOrCreate(
            ['email' => 'carlos@spotlight.com'],
            [
                'name' => 'Carlos Empresario',
                'password' => Hash::make('password'),
                'role' => 'business',
            ]
        );

        $companyTech = Company::firstOrCreate(
            ['user_id' => $carlos->id],
            [
                'name' => 'TechStore Pro',
                'description' => 'Soluciones tecnológicas, equipos informáticos y accesorios.',
                'category' => 'Tecnología',
                'address' => 'Carrera 7 # 8-45, Villa de San Diego de Ubaté',
                'phone' => '3101234567',
                'email' => 'techstore@spotlight.com',
                'status' => 'active',
                'latitude' => 5.3086,
                'longitude' => -73.8149,
            ]
        );

        // Actualizar como empresa formal
        $companyTech->update([
            'tipo_negocio' => 'formal',
            'razon_social' => 'TechStore Pro Colombia S.A.S.',
            'nit' => '901834521-4',
            'nombre_representante' => 'Carlos Eduardo Mendoza',
            'cedula_propietario' => '79845120',
            'email_representante' => 'gerencia@techstore.com',
            'status' => 'active',
        ]);

        // Perfil Fiscal DIAN Habilitado
        $fiscalProfile = CompanyFiscalProfile::updateOrCreate(
            ['company_id' => $companyTech->id],
            [
                'fiscal_status' => CompanyFiscalProfile::STATUS_ENABLED,
                'person_type' => 'persona_juridica',
                'id_type' => '31', // NIT
                'identification_number' => '901834521',
                'dv' => '4',
                'business_name' => 'TechStore Pro Colombia S.A.S.',
                'commercial_name' => 'TechStore Pro',
                'tax_regime' => 'responsable_iva',
                'tax_responsibilities' => ['O-13', 'O-15', 'O-23', 'O-47'],
                'ciiu_code' => '4741',
                'fiscal_address' => 'Carrera 7 # 8-45, Centro',
                'municipality_code' => '25843', // Ubaté
                'department_code' => '25',     // Cundinamarca
                'postal_code' => '250420',
                'country_code' => 'CO',
                'fiscal_email' => 'facturacion@techstore.com',
                'fiscal_phone' => '3101234567',
                'rut_document_path' => 'fiscal_docs/rut_techstore_sample.pdf',
                'camara_comercio_document_path' => 'fiscal_docs/cc_techstore_sample.pdf',
                'legal_rep_id_document_path' => 'fiscal_docs/cedula_rep_sample.pdf',
                'submitted_at' => now()->subDays(5),
                'verified_at' => now()->subDays(3),
                'verified_by_user_id' => $admin->id,
                'enabled_at' => now()->subDays(3),
            ]
        );

        // Configuración Técnica DIAN (Resolución de Prueba Sandbox)
        $billingConfig = BillingConfiguration::updateOrCreate(
            ['company_id' => $companyTech->id],
            [
                'provider' => 'sandbox',
                'environment' => 'sandbox',
                'resolution_number' => '18760000001',
                'resolution_prefix' => 'SETP',
                'range_from' => 1,
                'range_to' => 5000,
                'current_consecutive' => 101,
                'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
                'resolution_date_from' => now()->subMonths(1),
                'resolution_date_to' => now()->addMonths(11),
                'credit_note_prefix' => 'NC',
                'credit_note_consecutive' => 1,
                'debit_note_prefix' => 'ND',
                'debit_note_consecutive' => 1,
                'software_id' => 'e837df29-234b-4832-8df2-817349abef01',
                'software_pin' => '12345',
                'test_set_id' => 'test-set-spotlight-ubate-001',
                'is_connected' => true,
                'last_connection_test_at' => now()->subDays(2),
            ]
        );

        // Productos de la empresa con atributos fiscales
        $productsData = [
            [
                'name' => 'Audífonos Bluetooth Noise Cancelling',
                'description' => 'Audífonos inalámbricos de alta definición con cancelación activa de ruido.',
                'price' => 150000,
                'stock' => 25,
                'sku' => 'TECH-AUD-001',
                'tax_type' => '01', // IVA
                'tax_rate' => 19.00,
                'unit_measure' => '94',
                'standard_code' => '52161500',
                'active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Mouse Ergonómico Inalámbrico RGB',
                'description' => 'Sensor óptico de alta precisión con 6 niveles de DPI ajustables.',
                'price' => 85000,
                'stock' => 30,
                'sku' => 'TECH-MOU-002',
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'unit_measure' => '94',
                'standard_code' => '43211708',
                'active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Teclado Mecánico Switch Brown',
                'description' => 'Teclado para oficina y desarrollo con retroiluminación y distribución en español.',
                'price' => 220000,
                'stock' => 12,
                'sku' => 'TECH-KEY-003',
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'unit_measure' => '94',
                'standard_code' => '43211706',
                'active' => true,
                'featured' => false,
            ],
            [
                'name' => 'Cable USB-C Reforzado 2 Metros',
                'description' => 'Cable de carga rápida 65W compatible con laptops y teléfonos.',
                'price' => 25000,
                'stock' => 40,
                'sku' => 'TECH-CAB-004',
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'unit_measure' => '94',
                'standard_code' => '26121600',
                'active' => true,
                'featured' => false,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $createdProducts[] = Product::updateOrCreate(
                ['company_id' => $companyTech->id, 'name' => $data['name']],
                $data
            );
        }

        // Cliente para pedidos
        $customerAna = User::firstOrCreate(
            ['email' => 'ana@spotlight.com'],
            ['name' => 'Ana García', 'password' => Hash::make('password'), 'role' => 'user']
        );

        $customerLuis = User::firstOrCreate(
            ['email' => 'luis@spotlight.com'],
            ['name' => 'Luis Pérez', 'password' => Hash::make('password'), 'role' => 'user']
        );

        // 3. Crear Pedidos y Facturas de demostración (si aún no han sido creados)
        if (!ElectronicDocument::where('company_id', $companyTech->id)->where('prefix', 'SETP')->where('consecutive', 101)->exists()) {
            // Pedido A: Pendiente por facturar en /business/billing/sales
            $orderPending = Order::create([
                'user_id' => $customerAna->id,
                'company_id' => $companyTech->id,
                'total' => 279650, // 235.000 + IVA 19% = 279.650
                'status' => 'paid',
                'billing_status' => 'not_billed',
                'payment_method_code' => '48', // Tarjeta crédito
            ]);

            OrderDetail::create([
                'order_id' => $orderPending->id,
                'product_id' => $createdProducts[0]->id,
                'company_id' => $companyTech->id,
                'product_name' => $createdProducts[0]->name,
                'product_sku' => $createdProducts[0]->sku,
                'quantity' => 1,
                'unit_price' => 150000,
                'subtotal' => 150000,
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'tax_amount' => 28500,
            ]);

            OrderDetail::create([
                'order_id' => $orderPending->id,
                'product_id' => $createdProducts[1]->id,
                'company_id' => $companyTech->id,
                'product_name' => $createdProducts[1]->name,
                'product_sku' => $createdProducts[1]->sku,
                'quantity' => 1,
                'unit_price' => 85000,
                'subtotal' => 85000,
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'tax_amount' => 16150,
            ]);

            // Pedido B: Facturado previamente con factura electrónica DIAN emitida
            $orderBilled = Order::create([
                'user_id' => $customerLuis->id,
                'company_id' => $companyTech->id,
                'total' => 261800, // 220.000 + IVA = 261.800
                'status' => 'paid',
                'billing_status' => 'not_billed', // Servicio lo marcará como billed
                'payment_method_code' => '10', // Efectivo
            ]);

            OrderDetail::create([
                'order_id' => $orderBilled->id,
                'product_id' => $createdProducts[2]->id,
                'company_id' => $companyTech->id,
                'product_name' => $createdProducts[2]->name,
                'product_sku' => $createdProducts[2]->sku,
                'quantity' => 1,
                'unit_price' => 220000,
                'subtotal' => 220000,
                'tax_type' => '01',
                'tax_rate' => 19.00,
                'tax_amount' => 41800,
            ]);

            // Emitir factura electrónica SETP-101 mediante el servicio oficial
            /** @var ElectronicDocumentService $billingService */
            $billingService = app(ElectronicDocumentService::class);

            $invoice = $billingService->createInvoiceFromOrder(
                $companyTech,
                $orderBilled,
                [
                    'name' => 'Luis Fernando Pérez Gómez',
                    'id_type' => '13', // Cédula de Ciudadanía
                    'id_number' => '1018234567',
                    'email' => 'luis@spotlight.com',
                    'phone' => '3128901234',
                    'address' => 'Barrio Santa Bárbara, Carrera 5 # 10-12, Ubaté',
                    'municipality_code' => '25843',
                ],
                '10'
            );

            // 5. Emitir una segunda factura directa y su Nota Crédito correspondiente para ver ciclo completo
            $directInvoice = $billingService->createDirectInvoice(
                $companyTech,
                [
                    'name' => 'Empresas Unidas de Ubaté S.A.S.',
                    'id_type' => '31', // NIT
                    'id_number' => '900987654',
                    'dv' => '8',
                    'email' => 'contabilidad@empresasunidas.com',
                    'phone' => '3205557788',
                    'address' => 'Zona Industrial Ubaté Km 2',
                ],
                [
                    [
                        'description' => 'Mantenimiento y configuración de red empresarial',
                        'quantity' => 1,
                        'price_amount' => 180000,
                        'tax_rate' => 19.00,
                        'standard_code' => '81111800',
                    ]
                ],
                '30' // Transferencia bancaria
            );

            // Emitir Nota Crédito de anulación por concepto 2 (Anulación de factura electrónica)
            $creditNote = $billingService->createCreditNote(
                $directInvoice,
                '2',
                'Anulación solicitada por el cliente debido a cambio en los términos del contrato de mantenimiento.'
            );
        }

        // 6. CREAR EMPRESA FORMAL EN PROCESO DE VERIFICACIÓN (Para prueba de Administrador en /admin/fiscal-verifications)
        $userLacteos = User::firstOrCreate(
            ['email' => 'lacteos@spotlight.com'],
            [
                'name' => 'Roberto Sánchez',
                'password' => Hash::make('password'),
                'role' => 'business',
            ]
        );

        $companyLacteos = Company::firstOrCreate(
            ['user_id' => $userLacteos->id],
            [
                'name' => 'Lácteos San Diego Ubaté',
                'description' => 'Producción de quesos madurados, arequipe y derivados lácteos tradicionales.',
                'category' => 'Alimentos',
                'address' => 'Vereda San Diego, Finca El Paraíso, Ubaté',
                'phone' => '3145678901',
                'email' => 'lacteos@spotlight.com',
                'status' => 'active',
                'tipo_negocio' => 'formal',
                'razon_social' => 'Lácteos del Valle de Ubaté S.A.S.',
                'nit' => '901548921-3',
                'nombre_representante' => 'Roberto Sánchez Pinzón',
                'cedula_propietario' => '19456789',
                'email_representante' => 'gerencia@lacteosdiego.com',
            ]
        );

        CompanyFiscalProfile::updateOrCreate(
            ['company_id' => $companyLacteos->id],
            [
                'fiscal_status' => CompanyFiscalProfile::STATUS_IN_REVIEW,
                'person_type' => 'persona_juridica',
                'id_type' => '31',
                'identification_number' => '901548921',
                'dv' => '3',
                'business_name' => 'Lácteos del Valle de Ubaté S.A.S.',
                'commercial_name' => 'Lácteos San Diego Ubaté',
                'tax_regime' => 'responsable_iva',
                'tax_responsibilities' => ['O-13', 'O-47'],
                'ciiu_code' => '1051', // Elaboración de productos lácteos
                'fiscal_address' => 'Vereda San Diego, Finca El Paraíso',
                'municipality_code' => '25843',
                'department_code' => '25',
                'postal_code' => '250420',
                'country_code' => 'CO',
                'fiscal_email' => 'tributaria@lacteosdiego.com',
                'fiscal_phone' => '3145678901',
                'rut_document_path' => 'fiscal_docs/rut_sample.pdf',
                'camara_comercio_document_path' => 'fiscal_docs/camara_sample.pdf',
                'submitted_at' => now()->subDay(),
            ]
        );
    }
}
