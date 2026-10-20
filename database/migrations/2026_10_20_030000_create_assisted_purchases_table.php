<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assisted_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 30)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Comprador
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Comercio vendedor
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Producto solicitado
            $table->integer('quantity')->default(1);

            // Países de origen y destino
            $table->string('origin_country', 2); // 'CO' o 'MX'
            $table->string('destination_country', 2); // 'CO' o 'MX'

            // Ubicación de entrega en destino
            $table->string('destination_state', 100)->nullable();
            $table->string('destination_city', 100)->nullable();
            $table->text('destination_address')->nullable();
            $table->string('destination_postal_code', 20)->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->string('recipient_phone', 50)->nullable();
            $table->string('recipient_email', 150)->nullable();

            // Instantánea de precios y condiciones acordadas
            $table->decimal('original_price', 12, 2);
            $table->string('original_currency', 3)->default('COP');
            $table->decimal('shipping_cost_agreed', 12, 2)->default(0.00);
            $table->decimal('other_fees_agreed', 12, 2)->default(0.00); // Aduanas/gestión si aplica
            $table->decimal('total_agreed', 12, 2)->default(0.00);
            $table->string('payment_currency', 3)->default('COP');
            $table->string('payment_method_agreed', 100)->nullable();

            // Estados del flujo internacional asistido
            $table->enum('status', [
                'pending_review',        // Pendiente de revisión
                'in_negotiation',         // En negociación
                'pending_confirmation',  // Pendiente de confirmación
                'pending_payment',       // Pendiente de pago
                'payment_processing',    // Pago en proceso
                'paid',                  // Pagada
                'in_preparation',        // En preparación
                'shipped',               // Enviada
                'delivered',             // Entregada
                'cancelled',             // Cancelada
                'rejected',              // Rechazada
            ])->default('pending_review')->index();

            $table->text('buyer_notes')->nullable();
            $table->text('seller_notes')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier_name', 100)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->string('transaction_id', 150)->nullable();
            $table->string('estimated_delivery_days', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assisted_purchases');
    }
};
