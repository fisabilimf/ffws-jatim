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
        Schema::create('predicted_calculated_discharges', function (Blueprint $table) {
            $table->id();
            $table->string('mas_sensor_code', 100);
            $table->double('predicted_value');
            $table->double('predicted_discharge');
            $table->unsignedBigInteger('rating_curve_id');
            $table->dateTime('calculated_at');
            $table->timestamps();

            $table->index(['mas_sensor_code', 'calculated_at'], 'idx_pcd_sensor_ts');
            $table->index('rating_curve_id', 'idx_pcd_curve');
            $table->unique(['mas_sensor_code', 'calculated_at'], 'uq_pcd_sensor_ts');
            
            $table->foreign('mas_sensor_code', 'fk_pcd_sensor_code')
                  ->references('sensor_code')
                  ->on('mas_sensors')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('rating_curve_id', 'fk_pcd_curve_id')
                  ->references('id')
                  ->on('rating_curves')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicted_calculated_discharges');
    }
};