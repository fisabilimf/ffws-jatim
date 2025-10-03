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
        Schema::create('mas_upts', function (Blueprint $table) {
            $table->id();
            $table->string('river_basin_code', 100);
            $table->string('cities_code', 100);
            $table->string('upts_name');
            $table->string('upts_code', 100)->unique();
            $table->timestamps();

            $table->index('river_basin_code', 'fk_upt_basin_code');
            $table->index('cities_code', 'fk_upt_city_code');
            
            $table->foreign('river_basin_code', 'fk_upt_basin_code')
                  ->references('code')
                  ->on('mas_river_basins')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('cities_code', 'fk_upt_city_code')
                  ->references('cities_code')
                  ->on('mas_cities')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_upts');
    }
};