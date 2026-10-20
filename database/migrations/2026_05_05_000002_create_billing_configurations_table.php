<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('billing_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Proveedor tecnológico
            $table->string('provider', 50)->default('sandbox'); // 'sandbox', 'factus', 'siigo', etc.
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');

            // Credenciales del software / API (se manejarán encriptadas en el modelo)
            $table->text('api_token')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('software_id')->nullable();
            $table->string('software_pin')->nullable();
            $table->string('test_set_id')->nullable(); // ID del set de pruebas de habilitación DIAN

            // Resolución de Facturación DIAN
            $table->string('resolution_number', 50)->nullable(); // Ej: 18764000001
            $table->string('resolution_prefix', 10)->nullable(); // Ej: SETP, FE
            $table->unsignedBigInteger('range_from')->default(1);
            $table->unsignedBigInteger('range_to')->default(5000);
            $table->unsignedBigInteger('current_consecutive')->default(1);
            $table->text('technical_key')->nullable(); // Clave técnica DIAN para cálculo del CUFE
            $table->date('resolution_date_from')->nullable();
            $table->date('resolution_date_to')->nullable();

            // Configuración de notas crédito / débito
            $table->string('credit_note_prefix', 10)->default('NC');
            $table->unsignedBigInteger('credit_note_consecutive')->default(1);
            $table->string('debit_note_prefix', 10)->default('ND');
            $table->unsignedBigInteger('debit_note_consecutive')->default(1);

            // Estado de validación técnica
            $table->boolean('is_connected')->default(false);
            $table->timestamp('last_connection_test_at')->nullable();
            $table->text('last_connection_error')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_configurations');
    }
};
