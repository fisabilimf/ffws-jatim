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
        Schema::create('mas_sensors', function (Blueprint $table) {
            $table->id();
            $table->string('mas_device_code');
            $table->foreign('mas_device_code')->references('device_code')->on('mas_devices')->onUpdate('restrict')->onDelete('cascade');
            $table->string('name');
            $table->string('sensor_code')->unique();
            $table->enum('parameter', ['water_level', 'rainfall', 'discharge', 'temperature', 'humidity', 'wind_speed', 'wind_direction', 'pressure', 'battery_voltage', 'other']);
            $table->string('unit', 50);
            $table->string('description')->nullable();
            $table->string('mas_model_code')->nullable();
            $table->foreign('mas_model_code')->references('model_code')->on('mas_models')->onUpdate('restrict')->onDelete('set null');
            $table->double('threshold_safe')->nullable();
            $table->double('threshold_warning')->nullable();
            $table->double('threshold_danger')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->dateTime('last_seen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_sensors');
    }
};
