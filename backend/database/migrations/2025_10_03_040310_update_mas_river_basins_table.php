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
        Schema::table('mas_river_basins', function (Blueprint $table) {
            // Rename columns to match new schema
            $table->renameColumn('name', 'river_basins_name');
            $table->renameColumn('code', 'river_basins_code');
            
            // Add cities_code column and foreign key
            $table->string('cities_code', 100)->after('river_basins_code');
            
            // Add foreign key constraint
            $table->index('cities_code', 'fk_rbasin_city_code');
            $table->foreign('cities_code', 'fk_rbasin_city_code')
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
        Schema::table('mas_river_basins', function (Blueprint $table) {
            $table->dropForeign('fk_rbasin_city_code');
            $table->dropIndex('fk_rbasin_city_code');
            $table->dropColumn('cities_code');
            $table->renameColumn('river_basins_name', 'name');
            $table->renameColumn('river_basins_code', 'code');
        });
    }
};