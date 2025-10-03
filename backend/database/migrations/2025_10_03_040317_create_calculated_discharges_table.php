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
        Schema::create('calculated_discharges', function (Blueprint $table) {
            $table->id();
            $table->string('mas_sensor_code', 100);
            $table->double('sensor_value');
            $table->double('sensor_discharge');
            $table->unsignedBigInteger('rating_curve_id');
            $table->dateTime('calculated_at');
            $table->timestamps();

            $table->index(['mas_sensor_code', 'calculated_at'], 'idx_cd_sensor_ts');
            $table->index('rating_curve_id', 'idx_cd_curve');
            $table->unique(['mas_sensor_code', 'calculated_at'], 'uq_cd_sensor_ts');
            
            $table->foreign('mas_sensor_code', 'fk_cd_sensor_code')
                  ->references('sensor_code')
                  ->on('mas_sensors')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('rating_curve_id', 'fk_cd_curve_id')
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
        Schema::dropIfExists('calculated_discharges');
    }
};