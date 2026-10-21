<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // slug
            $table->string('label'); // display name
            $table->string('icon')->nullable(); // bootstrap icon class
            $table->string('color_class')->nullable(); // optional css class
            $table->json('theme')->nullable(); // JSON config for colors
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
