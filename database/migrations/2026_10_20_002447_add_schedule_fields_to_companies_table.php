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
            $table->string('opening_time', 10)->nullable()->default('08:00')->after('website');
            $table->string('closing_time', 10)->nullable()->default('19:00')->after('opening_time');
            $table->json('operating_days')->nullable()->after('closing_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['opening_time', 'closing_time', 'operating_days']);
        });
    }
};
