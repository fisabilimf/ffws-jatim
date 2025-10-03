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
        Schema::table('mas_sensors', function (Blueprint $table) {
            // Add the mas_device_code column
            $table->string('mas_device_code', 255)->nullable()->after('device_id');
            
            // Add foreign key constraint for mas_device_code
            $table->index('mas_device_code', 'fk_sensor_device_code');
            $table->foreign('mas_device_code', 'fk_sensor_device_code')
                  ->references('code')
                  ->on('mas_devices')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mas_sensors', function (Blueprint $table) {
            $table->dropForeign('fk_sensor_device_code');
            $table->dropIndex('fk_sensor_device_code');
            $table->dropColumn('mas_device_code');
        });
    }
};