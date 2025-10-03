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
        Schema::table('mas_scalers', function (Blueprint $table) {
            // Add mas_model_code column
            $table->string('mas_model_code', 255)->nullable()->after('mas_model_id');
            
            // Add foreign key constraint
            $table->index('mas_model_code', 'fk_scaler_model_code');
            $table->foreign('mas_model_code', 'fk_scaler_model_code')
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
        Schema::table('mas_scalers', function (Blueprint $table) {
            $table->dropForeign('fk_scaler_model_code');
            $table->dropIndex('fk_scaler_model_code');
            $table->dropColumn('mas_model_code');
        });
    }
};