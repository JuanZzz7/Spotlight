<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('billing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('electronic_document_id')->nullable()->constrained('electronic_documents')->nullOnDelete();
            $table->string('action', 50); // 'emit_invoice', 'emit_credit_note', 'check_status', 'test_connection'
            $table->string('provider', 50);
            $table->string('endpoint', 255)->nullable();
            $table->string('idempotency_key', 100)->nullable()->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('http_status')->nullable();
            $table->boolean('is_success')->default(false);
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_logs');
    }
};
