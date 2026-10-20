<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('payment_reference', 80)->nullable()->index();
            $table->string('provider', 50)->default('wompi');
            $table->string('event_type', 80)->default('transaction.updated');
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 30)->default('info'); // info, success, warning, error
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
