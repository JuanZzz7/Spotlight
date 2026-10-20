<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('electronic_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Tipo de documento: 01: Factura de Venta, 91: Nota Crédito, 92: Nota Débito
            $table->string('document_type', 5)->default('01');
            $table->foreignId('parent_document_id')->nullable()->constrained('electronic_documents')->nullOnDelete();
            $table->string('correction_concept_code', 10)->nullable();
            $table->text('correction_reason')->nullable();

            // Numeración y resolución
            $table->string('prefix', 10);
            $table->unsignedBigInteger('consecutive');
            $table->string('full_number', 50)->index();
            $table->string('uuid_cufe', 255)->nullable()->index(); // CUFE / CUDE
            $table->date('issue_date');
            $table->time('issue_time');
            $table->date('due_date')->nullable();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');

            // Snapshot inmutable del emisor
            $table->string('issuer_nit', 50);
            $table->string('issuer_dv', 2)->nullable();
            $table->string('issuer_business_name', 255);
            $table->string('issuer_tax_regime', 50)->nullable();
            $table->string('issuer_address', 255)->nullable();
            $table->string('issuer_phone', 50)->nullable();
            $table->string('issuer_email', 255)->nullable();
            $table->string('issuer_municipality_code', 10)->default('25843');

            // Snapshot inmutable del adquirente (cliente)
            $table->string('customer_id_type', 10)->default('31');
            $table->string('customer_id_number', 50)->default('222222222222');
            $table->string('customer_dv', 2)->nullable();
            $table->string('customer_name', 255)->default('Consumidor Final');
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_address', 255)->nullable();
            $table->string('customer_municipality_code', 10)->default('25843');

            // Totales monetarios según reglas DIAN UBL 2.1
            $table->decimal('line_extension_amount', 12, 2); // Subtotal sin impuestos
            $table->decimal('tax_exclusive_amount', 12, 2);  // Base gravable
            $table->decimal('tax_inclusive_amount', 12, 2);  // Subtotal con impuestos
            $table->decimal('allowance_total_amount', 12, 2)->default(0.00); // Descuentos
            $table->decimal('tax_total_amount', 12, 2)->default(0.00); // Total IVA / Impuestos
            $table->decimal('payable_amount', 12, 2); // Monto final a pagar
            $table->string('payment_method_code', 10)->default('10'); // 10 Efectivo, 48 Tarjeta crédito, etc.
            $table->string('payment_means_code', 10)->default('1'); // 1 Contado, 2 Crédito

            // Estado y Trazabilidad DIAN
            $table->enum('status', ['draft', 'sent', 'validated', 'rejected', 'error', 'cancelled'])->default('draft');
            $table->string('dian_status', 50)->nullable(); // DIAN_ACEPTADO, DIAN_RECHAZADO
            $table->text('dian_response_message')->nullable();
            $table->json('dian_validation_errors')->nullable();
            $table->string('xml_path', 255)->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->text('qr_content')->nullable();
            $table->string('provider_track_id', 255)->nullable();
            $table->json('provider_raw_response')->nullable();

            // Fechas de ciclo de vida
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('delivered_to_customer_at')->nullable();

            $table->timestamps();

            // Índice de unicidad para evitar duplicación de facturas de un mismo prefijo y consecutivo por comercio
            $table->unique(['company_id', 'document_type', 'prefix', 'consecutive'], 'uniq_doc_consecutive');
        });

        Schema::create('electronic_document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electronic_document_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('line_number');
            $table->string('product_code', 50)->nullable();
            $table->string('description', 255);
            $table->decimal('quantity', 10, 2);
            $table->string('unit_measure', 10)->default('94');
            $table->decimal('price_amount', 12, 2);
            $table->decimal('line_extension_amount', 12, 2); // Cantidad * Precio
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->string('tax_type', 10)->default('01'); // 01 IVA
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('tax_base_amount', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('total_item_amount', 12, 2); // line_extension_amount - discount + tax_amount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electronic_document_items');
        Schema::dropIfExists('electronic_documents');
    }
};
