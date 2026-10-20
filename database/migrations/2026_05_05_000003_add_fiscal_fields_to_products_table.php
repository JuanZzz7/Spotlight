<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)->nullable()->after('name');
            $table->string('tax_type', 10)->default('01')->after('price'); // 01: IVA, 04: INC (Impuesto Nacional al Consumo), 03: ICA, ZZ: Excluido/No aplica
            $table->decimal('tax_rate', 5, 2)->default(19.00)->after('tax_type'); // 19.00, 5.00, 0.00
            $table->string('unit_measure', 10)->default('94')->after('tax_rate'); // 94: Unidad, KGM: Kilogramo, etc. (Catálogo DIAN)
            $table->string('standard_code', 50)->nullable()->after('unit_measure'); // Estándar de producto (UNSPSC o propio)
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'tax_type',
                'tax_rate',
                'unit_measure',
                'standard_code'
            ]);
        });
    }
};
