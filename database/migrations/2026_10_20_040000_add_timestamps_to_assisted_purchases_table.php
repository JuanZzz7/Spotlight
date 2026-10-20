<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assisted_purchases', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('shipped_at')->nullable()->after('paid_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('assisted_purchases', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'shipped_at', 'delivered_at']);
        });
    }
};
