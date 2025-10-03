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
        Schema::create('mas_scalers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mas_model_id')->constrained('mas_models')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('mas_sensor_id')->nullable()->constrained('mas_sensors')->onUpdate('restrict')->onDelete('set null');
            $table->string('name');
            $table->string('scaler_code')->unique();
            $table->enum('io_axis', ['x', 'y']);
            $table->enum('technique', ['standard', 'minmax', 'robust', 'custom'])->default('custom');
            $table->string('version', 64)->nullable();
            $table->string('file_path', 512);
            $table->char('file_hash_sha256', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique Index
            $table->unique(['mas_model_id', 'mas_sensor_id', 'io_axis', 'is_active'], 'uk_model_sensor_axis_active');

            // Other Indexes
            $table->index('io_axis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_scalers');
    }
};
