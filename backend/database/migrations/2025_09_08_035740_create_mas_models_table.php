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
        Schema::create('mas_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model_code');
            $table->string('model_type');
            $table->string('version')->nullable();
            $table->string('description')->nullable();
            $table->string('file_path')->nullable();
            $table->tinyInteger('n_steps_in')->nullable();
            $table->tinyInteger('n_steps_out')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_models');
    }
};
