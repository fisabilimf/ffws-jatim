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
        Schema::create('data_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('mas_sensor_code');
            $table->foreign('mas_sensor_code')->references('sensor_code')->on('mas_sensors')->onUpdate('restrict')->onDelete('restrict');
            $table->string('mas_model_code');
            $table->foreign('mas_model_code')->references('model_code')->on('mas_models')->onUpdate('restrict')->onDelete('restrict');
            $table->dateTime('prediction_run_at');
            $table->dateTime('prediction_for_ts');
            $table->double('predicted_value');
            $table->double('confidence_score')->nullable();
            $table->enum('threshold_prediction_status', ['safe', 'warning', 'danger'])->nullable();
            $table->timestamps();

            // Indexes
            $table->index('prediction_run_at');
            $table->index('prediction_for_ts'); // For future time queries
            $table->index('mas_sensor_code'); // For sensor-based queries
            $table->index(['mas_sensor_code', 'prediction_for_ts']); // Compound index for time-series predictions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_predictions');
    }
};
