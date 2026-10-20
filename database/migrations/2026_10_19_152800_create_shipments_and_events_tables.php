<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('tracking_number', 60)->unique();
            $table->string('carrier', 40)->default('coordinadora'); // coordinadora, servientrega, interrapidisimo, envia, dhl, spotlight_express
            $table->string('carrier_name', 80)->default('Coordinadora Mercantil');
            $table->string('status', 30)->default('preparing'); // pending, preparing, dispatched, in_transit, out_for_delivery, delivered, failed
            $table->string('origin_city', 100)->default('Bogotá D.C.');
            $table->string('destination_city', 100)->default('Bogotá D.C.');
            $table->text('destination_address');
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 40)->nullable();
            $table->string('current_location', 150)->nullable();
            $table->dateTime('estimated_delivery_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('status', 40);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('location', 150)->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipments');
    }
};
