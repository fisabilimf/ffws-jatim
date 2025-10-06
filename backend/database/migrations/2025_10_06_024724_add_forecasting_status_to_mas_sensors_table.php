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
            $table->enum('forecasting_status', ['stopped', 'running', 'paused'])->default('stopped')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mas_sensors', function (Blueprint $table) {
            $table->dropColumn('forecasting_status');
        });
    }
};
