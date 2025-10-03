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
        Schema::create('device_values', function (Blueprint $table) {
            $table->id();
            $table->string('mas_device_code', 100);
            $table->string('mas_river_basin_code', 100)->nullable();
            $table->string('mas_watershed_code', 100)->nullable();
            $table->string('mas_city_code', 100)->nullable();
            $table->string('mas_regency_code', 100)->nullable();
            $table->string('mas_village_code', 100)->nullable();
            $table->string('mas_upt_code', 100)->nullable();
            $table->string('mas_uptd_code', 100)->nullable();
            $table->string('mas_device_parameter_code', 100)->nullable();
            $table->string('name')->nullable();
            $table->string('icon_path', 500)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->double('elevation')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->timestamps();

            $table->index('mas_river_basin_code', 'idx_dev_rbasin');
            $table->index('mas_watershed_code', 'idx_dev_watershed');
            $table->index('mas_city_code', 'idx_dev_city');
            $table->index('mas_regency_code', 'idx_dev_regency');
            $table->index('mas_village_code', 'idx_dev_village');
            $table->index('mas_upt_code', 'idx_dev_upt');
            $table->index('mas_uptd_code', 'idx_dev_uptd');
            $table->index('mas_device_parameter_code', 'idx_dev_param');
            $table->index('mas_device_code', 'fk_dv_device_code');

            // Foreign key constraints
            $table->foreign('mas_device_code', 'fk_dv_device_code')
                  ->references('code')
                  ->on('mas_devices')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_river_basin_code', 'fk_dv_rbasin_code')
                  ->references('river_basins_code')
                  ->on('mas_river_basins')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_watershed_code', 'fk_dv_ws_code')
                  ->references('watersheds_code')
                  ->on('mas_watersheds')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_city_code', 'fk_dv_city_code')
                  ->references('cities_code')
                  ->on('mas_cities')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_regency_code', 'fk_dv_regency_code')
                  ->references('regencies_code')
                  ->on('mas_regencies')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_village_code', 'fk_dv_village_code')
                  ->references('villages_code')
                  ->on('mas_villages')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_upt_code', 'fk_dv_upt_code')
                  ->references('upts_code')
                  ->on('mas_upts')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_uptd_code', 'fk_dv_uptd_code')
                  ->references('code')
                  ->on('mas_uptds')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_device_parameter_code', 'fk_dv_device_param')
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
        Schema::dropIfExists('device_values');
    }
};