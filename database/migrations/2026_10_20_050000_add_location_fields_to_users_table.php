<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'phone_prefix')) {
                $table->string('phone_prefix', 10)->default('+57')->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['state', 'postal_code', 'phone_prefix']);
        });
    }
};
