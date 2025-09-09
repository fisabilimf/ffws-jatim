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
        Schema::create('mas_devices', function (Blueprint $table) {
            $table->id();
            $table->string('mas_river_basin_code');
            $table->foreign('mas_river_basin_code')->references('code')->on('mas_river_basins')->onUpdate('restrict')->onDelete('restrict');
            $table->string('name');
            $table->string('device_code')->unique();
            $table->double('latitude');
            $table->double('longitude');
            $table->double('elevation_m');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_devices');
    }
};
