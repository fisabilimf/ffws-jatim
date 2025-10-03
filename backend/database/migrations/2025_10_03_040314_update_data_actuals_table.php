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
        Schema::table('data_actuals', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['mas_sensor_id']);
            $table->dropColumn('mas_sensor_id');
            
            // Update threshold_status enum values
            $table->dropColumn('threshold_status');
            $table->enum('threshold_status', ['normal', 'watch', 'warning', 'danger', 'unknown'])
                  ->default('unknown')
                  ->after('received_at');
            
            // Add generated column for received_date
            $table->date('received_date')
                  ->storedAs('cast(received_at as date)')
                  ->after('threshold_status');
            
            // Add proper indexes
            $table->index(['mas_sensor_code', 'received_at'], 'idx_da_sensor');
            $table->unique(['mas_sensor_code', 'received_at'], 'uq_da_sensor_ts');
            $table->index('received_date', 'idx_da_date');
            
            // Add foreign key constraint
            $table->foreign('mas_sensor_code', 'fk_da_sensor_code')
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
        Schema::table('data_actuals', function (Blueprint $table) {
            $table->dropForeign('fk_da_sensor_code');
            $table->dropIndex('idx_da_sensor');
            $table->dropUnique('uq_da_sensor_ts');
            $table->dropIndex('idx_da_date');
            $table->dropColumn('received_date');
            $table->dropColumn('threshold_status');
            
            $table->foreignId('mas_sensor_id')->after('id')->constrained('mas_sensors');
            $table->enum('threshold_status', ['safe', 'warning', 'danger'])->nullable()->after('received_at');
        });
    }
};