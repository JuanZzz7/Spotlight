<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('billing_status', 30)->default('not_billed')->after('status'); // not_billed, in_process, billed, exempt
            $table->string('payment_method_code', 10)->default('10')->after('billing_status'); // 10: Efectivo, 48: Tarjeta crédito, 49: Tarjeta débito, 31: Transferencia
            $table->date('payment_due_date')->nullable()->after('payment_method_code');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            // Snapshots históricos inmutables
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_sku', 50)->nullable()->after('product_name');
            $table->string('tax_type', 10)->default('01')->after('unit_price');
            $table->decimal('tax_rate', 5, 2)->default(19.00)->after('tax_type');
            $table->decimal('tax_amount', 10, 2)->default(0.00)->after('tax_rate');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'product_name',
                'product_sku',
                'tax_type',
                'tax_rate',
                'tax_amount',
                'discount_amount'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'billing_status',
                'payment_method_code',
                'payment_due_date'
            ]);
        });
    }
};
