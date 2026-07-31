<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('notify_all')->default(false);
            $table->boolean('notify_free')->default(false);
            $table->boolean('notify_subscribers')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['notify_all', 'notify_free', 'notify_subscribers']);
        });
    }
};
