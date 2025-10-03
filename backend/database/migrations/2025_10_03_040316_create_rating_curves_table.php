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
        Schema::create('rating_curves', function (Blueprint $table) {
            $table->id();
            $table->string('mas_sensor_code', 100);
            $table->enum('formula_type', ['power', 'polynomial', 'exponential', 'custom']);
            $table->double('a');
            $table->double('b')->nullable();
            $table->double('c')->nullable();
            $table->date('effective_date');
            $table->timestamps();

            $table->index(['mas_sensor_code', 'effective_date'], 'idx_rc_sensor');
            $table->foreign('mas_sensor_code', 'fk_rc_sensor_code')
                  ->references('sensor_code')
                  ->on('mas_sensors')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_curves');
    }
};