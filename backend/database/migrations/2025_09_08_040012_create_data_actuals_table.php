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
        Schema::create('data_actuals', function (Blueprint $table) {
            $table->id();
            $table->string('mas_sensor_code');
            $table->foreign('mas_sensor_code')->references('sensor_code')->on('mas_sensors')->onUpdate('restrict')->onDelete('restrict');
            $table->double('value');
            $table->dateTime('received_at');
            $table->enum('threshold_status', ['safe', 'warning', 'danger'])->nullable();
            $table->timestamps();

            // Indexes
            $table->index('received_at');
            $table->index('mas_sensor_code'); // For faster sensor queries
            $table->index(['mas_sensor_code', 'received_at']); // Compound index for time-series queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_actuals');
    }
};
