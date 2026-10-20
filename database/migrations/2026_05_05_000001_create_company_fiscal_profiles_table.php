<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_fiscal_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Estado de habilitación fiscal independiente
            $table->enum('fiscal_status', [
                'not_requested',
                'pending_documentation',
                'in_review',
                'rejected',
                'verified',
                'pending_technical_setup',
                'enabled',
                'suspended'
            ])->default('not_requested');

            $table->text('status_notes')->nullable(); // Motivo de rechazo o suspensión
            
            // Datos fiscales reglamentarios DIAN
            $table->enum('person_type', ['persona_juridica', 'persona_natural'])->default('persona_juridica');
            $table->string('id_type', 10)->default('31'); // 31: NIT, 13: Cédula de ciudadanía, 22: Cédula de extranjería, etc.
            $table->string('identification_number', 50)->nullable();
            $table->string('dv', 2)->nullable(); // Dígito de verificación
            $table->string('business_name')->nullable(); // Razón social registrada en RUT
            $table->string('commercial_name')->nullable(); // Nombre comercial
            $table->string('tax_regime', 50)->default('responsable_iva'); // responsable_iva (48), no_responsable_iva (49), regimen_simple, gran_contribuyente (13)
            $table->json('tax_responsibilities')->nullable(); // Códigos DIAN: ["O-13", "O-15", "O-23", "O-47", "R-99-PN"]
            $table->string('ciiu_code', 10)->nullable(); // Código CIIU actividad económica principal
            
            // Ubicación fiscal
            $table->string('fiscal_address')->nullable();
            $table->string('municipality_code', 10)->default('25843'); // Ubaté DANE
            $table->string('department_code', 10)->default('25'); // Cundinamarca DANE
            $table->string('postal_code', 10)->default('250420');
            $table->string('country_code', 10)->default('CO');
            $table->string('fiscal_email')->nullable();
            $table->string('fiscal_phone', 30)->nullable();

            // Documentación de respaldo
            $table->string('rut_document_path')->nullable();
            $table->string('camara_comercio_document_path')->nullable();
            $table->string('legal_rep_id_document_path')->nullable();

            // Trazabilidad de verificación
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('enabled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_fiscal_profiles');
    }
};
