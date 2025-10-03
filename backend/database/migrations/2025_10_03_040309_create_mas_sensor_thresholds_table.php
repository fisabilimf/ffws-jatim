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
        Schema::create('mas_sensor_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('sensor_thresholds_name');
            $table->string('sensor_thresholds_code', 100)->unique();
            $table->string('sensor_thresholds_value_1', 50)->nullable();
            $table->string('sensor_thresholds_value_1_color', 20)->nullable();
            $table->string('sensor_thresholds_value_2', 50)->nullable();
            $table->string('sensor_thresholds_value_2_color', 20)->nullable();
            $table->string('sensor_thresholds_value_3', 50)->nullable();
            $table->string('sensor_thresholds_value_3_color', 20)->nullable();
            $table->string('sensor_thresholds_value_4', 50)->nullable();
            $table->string('sensor_thresholds_value_4_color', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_sensor_thresholds');
    }
};