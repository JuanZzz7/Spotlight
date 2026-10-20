<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('website');
            $table->string('account_type', 30)->nullable()->after('bank_name'); // 'ahorros', 'corriente'
            $table->string('account_number', 50)->nullable()->after('account_type');
            $table->string('account_holder_name', 150)->nullable()->after('account_number');
            $table->string('account_holder_doc_type', 10)->nullable()->after('account_holder_name'); // 'CC', 'NIT', 'CE', 'PP'
            $table->string('account_holder_doc_number', 30)->nullable()->after('account_holder_doc_type');
            $table->string('wompi_merchant_id', 100)->nullable()->after('account_holder_doc_number');
            $table->string('split_rule_id', 100)->nullable()->after('wompi_merchant_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_type',
                'account_number',
                'account_holder_name',
                'account_holder_doc_type',
                'account_holder_doc_number',
                'wompi_merchant_id',
                'split_rule_id',
            ]);
        });
    }
};
