<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference', 80)->nullable()->unique()->after('company_id');
            $table->string('payment_reference', 80)->nullable()->index()->after('reference');
            $table->string('transaction_id', 120)->nullable()->index()->after('payment_reference');
            $table->string('payment_method', 50)->nullable()->after('transaction_id');
            $table->string('payment_status', 30)->default('pending')->after('payment_method'); // pending, processing, approved, declined, voided, error
            $table->string('order_status', 30)->default('pending_payment')->after('payment_status'); // pending_payment, confirmed, in_preparation, shipped, delivered, cancelled
            $table->json('shipping_address')->nullable()->after('order_status');
            $table->decimal('subtotal_company', 12, 2)->default(0.00)->after('total');
            $table->decimal('shipping_cost_company', 10, 2)->default(0.00)->after('subtotal_company');
            $table->decimal('platform_fee', 10, 2)->default(0.00)->after('shipping_cost_company');
            $table->decimal('net_company_amount', 12, 2)->default(0.00)->after('platform_fee');
            $table->string('settlement_status', 30)->default('pending')->after('net_company_amount'); // pending, processing, settled, failed
            $table->decimal('total_company_settlement', 12, 2)->default(0.00)->after('settlement_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'reference',
                'payment_reference',
                'transaction_id',
                'payment_method',
                'payment_status',
                'order_status',
                'shipping_address',
                'subtotal_company',
                'shipping_cost_company',
                'platform_fee',
                'net_company_amount',
                'settlement_status',
                'total_company_settlement',
            ]);
        });
    }
};
