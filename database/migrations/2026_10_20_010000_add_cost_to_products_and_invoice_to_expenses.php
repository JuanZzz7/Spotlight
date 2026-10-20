<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'cost_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('cost_price', 10, 2)->default(0.00)->after('price')->nullable();
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'category')) {
                    $table->string('category')->default('Operativo')->after('concept')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'invoice_path')) {
                    $table->string('invoice_path')->nullable()->after('description');
                }
                if (!Schema::hasColumn('expenses', 'invoice_filename')) {
                    $table->string('invoice_filename')->nullable()->after('invoice_path');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'cost_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('expenses', 'category')) $cols[] = 'category';
                if (Schema::hasColumn('expenses', 'invoice_path')) $cols[] = 'invoice_path';
                if (Schema::hasColumn('expenses', 'invoice_filename')) $cols[] = 'invoice_filename';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
