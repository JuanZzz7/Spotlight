<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ampliación de empresas para operación multinacional (Colombia + México)
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'country')) {
                $table->string('country', 2)->default('CO')->after('name')->index();
            }
            if (!Schema::hasColumn('companies', 'state')) {
                $table->string('state', 100)->nullable()->after('country');
            }
            if (!Schema::hasColumn('companies', 'city')) {
                $table->string('city', 100)->nullable()->after('state');
            }
            if (!Schema::hasColumn('companies', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('city');
            }
            if (!Schema::hasColumn('companies', 'currency')) {
                $table->string('currency', 3)->default('COP')->after('postal_code');
            }
            if (!Schema::hasColumn('companies', 'phone_prefix')) {
                $table->string('phone_prefix', 10)->default('+57')->after('phone');
            }
            if (!Schema::hasColumn('companies', 'tax_id')) {
                $table->string('tax_id', 50)->nullable()->after('nit'); // RFC en MX, NIT en CO
            }
        });

        // 2. Moneda y país en catálogo de productos
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'country')) {
                $table->string('country', 2)->default('CO')->after('company_id')->index();
            }
            if (!Schema::hasColumn('products', 'currency')) {
                $table->string('currency', 3)->default('COP')->after('price');
            }
        });

        // 3. Moneda, país y pasarela en órdenes de compra
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'country')) {
                $table->string('country', 2)->default('CO')->after('company_id')->index();
            }
            if (!Schema::hasColumn('orders', 'currency')) {
                $table->string('currency', 3)->default('COP')->after('total');
            }
            if (!Schema::hasColumn('orders', 'payment_gateway')) {
                $table->string('payment_gateway', 30)->default('wompi')->after('payment_method');
            }
        });

        // Actualizar datos históricos existentes para que sean coherentes con Colombia
        DB::table('companies')->whereNull('country')->orWhere('country', '')->update([
            'country' => 'CO',
            'currency' => 'COP',
            'phone_prefix' => '+57',
            'state' => 'Cundinamarca',
            'city' => 'Ubaté'
        ]);

        DB::table('products')->whereNull('country')->orWhere('country', '')->update([
            'country' => 'CO',
            'currency' => 'COP'
        ]);

        DB::table('orders')->whereNull('country')->orWhere('country', '')->update([
            'country' => 'CO',
            'currency' => 'COP',
            'payment_gateway' => 'wompi'
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['country', 'currency', 'payment_gateway']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['country', 'currency']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['country', 'state', 'city', 'postal_code', 'currency', 'phone_prefix', 'tax_id']);
        });
    }
};
