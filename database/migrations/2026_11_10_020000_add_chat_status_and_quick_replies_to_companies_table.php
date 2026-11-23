<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'chat_status')) {
                $table->string('chat_status', 20)->default('online')->after('status');
            }
            if (!Schema::hasColumn('companies', 'chat_visibility')) {
                $table->boolean('chat_visibility')->default(true)->after('chat_status');
            }
            if (!Schema::hasColumn('companies', 'chat_quick_replies')) {
                $table->json('chat_quick_replies')->nullable()->after('chat_visibility');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'chat_quick_replies')) {
                $table->dropColumn('chat_quick_replies');
            }
            if (Schema::hasColumn('companies', 'chat_visibility')) {
                $table->dropColumn('chat_visibility');
            }
            if (Schema::hasColumn('companies', 'chat_status')) {
                $table->dropColumn('chat_status');
            }
        });
    }
};

