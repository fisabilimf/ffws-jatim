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
        Schema::table('data_predictions', function (Blueprint $table) {
            // Update threshold_prediction_status enum values
            $table->dropColumn('threshold_prediction_status');
            $table->enum('threshold_prediction_status', ['normal', 'watch', 'warning', 'danger', 'unknown'])
                  ->default('unknown')
                  ->after('confidence_score');
            
            // Add mas_model_code column
            $table->string('mas_model_code', 255)->nullable()->after('mas_model_id');
            
            // Add proper indexes
            $table->index(['mas_sensor_code', 'prediction_for_ts'], 'idx_dp_sensor_ts');
            $table->index(['mas_model_code', 'prediction_run_at'], 'idx_dp_model');
            $table->unique(['mas_sensor_code', 'prediction_for_ts', 'mas_model_code'], 'uq_dp_sensor_t_model');
            
            // Add foreign key constraints
            $table->foreign('mas_sensor_code', 'fk_dp_sensor_code')
                  ->references('sensor_code')
                  ->on('mas_sensors')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('mas_model_code', 'fk_dp_model_code')
                  ->references('model_code')
                  ->on('mas_models')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_predictions', function (Blueprint $table) {
            $table->dropForeign('fk_dp_sensor_code');
            $table->dropForeign('fk_dp_model_code');
            $table->dropIndex('idx_dp_sensor_ts');
            $table->dropIndex('idx_dp_model');
            $table->dropUnique('uq_dp_sensor_t_model');
            $table->dropColumn('mas_model_code');
            $table->dropColumn('threshold_prediction_status');
            
            $table->enum('threshold_prediction_status', ['safe', 'warning', 'danger'])->nullable()->after('confidence_score');
        });
    }
};