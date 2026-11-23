<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('has_weekend_schedule')->default(false)->after('closing_time');
            $table->time('weekend_opening_time')->nullable()->after('has_weekend_schedule');
            $table->time('weekend_closing_time')->nullable()->after('weekend_opening_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['has_weekend_schedule', 'weekend_opening_time', 'weekend_closing_time']);
        });
    }
};
