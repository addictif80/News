<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popup_modals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('content');
            $table->string('trigger_type')->default('delay'); // load|delay|scroll|exit_intent
            $table->unsignedInteger('trigger_value')->nullable(); // seconds or scroll %
            $table->json('target_pages')->nullable(); // null = all pages
            $table->unsignedInteger('display_frequency_days')->default(1);
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_modals');
    }
};
