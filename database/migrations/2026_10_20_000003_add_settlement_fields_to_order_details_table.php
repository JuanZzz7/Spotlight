<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('platform_fee', 10, 2)->default(0.00)->after('discount_amount');
            $table->decimal('company_payout', 12, 2)->default(0.00)->after('platform_fee');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['platform_fee', 'company_payout']);
        });
    }
};
