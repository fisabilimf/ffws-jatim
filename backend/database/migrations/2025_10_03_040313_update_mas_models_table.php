<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mas_models', function (Blueprint $table) {
            // Add unique constraint on model_code
            $table->unique('model_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mas_models', function (Blueprint $table) {
            $table->dropUnique(['model_code']);
        });
    }
};