<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['article', 'page', 'homepage']);
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->json('grapesjs_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
