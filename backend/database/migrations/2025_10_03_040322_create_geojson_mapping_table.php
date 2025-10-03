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
        Schema::create('geojson_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('geojson_code', 100)->unique();
            $table->string('mas_device_code', 100)->nullable();
            $table->string('mas_river_basin_code', 100)->nullable();
            $table->string('mas_watershed_code', 100)->nullable();
            $table->string('mas_city_code', 100)->nullable();
            $table->string('mas_regency_code', 100)->nullable();
            $table->string('mas_village_code', 100)->nullable();
            $table->string('mas_upt_code', 100)->nullable();
            $table->string('mas_uptd_code', 100)->nullable();
            $table->string('mas_device_parameter_code', 100)->nullable();
            $table->string('value_min', 50)->nullable();
            $table->string('value_max', 50)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('version', 50)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('properties_content', 1000)->nullable();
            $table->timestamps();

            $table->index('mas_device_code', 'idx_gm_device');
            $table->index(['mas_river_basin_code', 'mas_watershed_code', 'mas_city_code', 'mas_regency_code', 'mas_village_code'], 'idx_gm_territory');
            $table->index('mas_watershed_code', 'fk_gm_ws_code');
            $table->index('mas_city_code', 'fk_gm_city_code');
            $table->index('mas_regency_code', 'fk_gm_regency_code');
            $table->index('mas_village_code', 'fk_gm_village_code');
            $table->index('mas_upt_code', 'fk_gm_upt_code');
            $table->index('mas_uptd_code', 'fk_gm_uptd_code');
            $table->index('mas_device_parameter_code', 'fk_gm_device_param');

            // Foreign key constraints
            $table->foreign('mas_device_code', 'fk_gm_device_code')
                  ->references('code')
                  ->on('mas_devices')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_river_basin_code', 'fk_gm_rbasin_code')
                  ->references('river_basins_code')
                  ->on('mas_river_basins')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_watershed_code', 'fk_gm_ws_code')
                  ->references('watersheds_code')
                  ->on('mas_watersheds')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_city_code', 'fk_gm_city_code')
                  ->references('cities_code')
                  ->on('mas_cities')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_regency_code', 'fk_gm_regency_code')
                  ->references('regencies_code')
                  ->on('mas_regencies')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_village_code', 'fk_gm_village_code')
                  ->references('villages_code')
                  ->on('mas_villages')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_upt_code', 'fk_gm_upt_code')
                  ->references('upts_code')
                  ->on('mas_upts')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_uptd_code', 'fk_gm_uptd_code')
                  ->references('code')
                  ->on('mas_uptds')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_device_parameter_code', 'fk_gm_device_param')
                  ->references('code')
                  ->on('mas_device_parameters')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geojson_mapping');
    }
};