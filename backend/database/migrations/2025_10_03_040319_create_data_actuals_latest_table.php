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
        Schema::create('data_actuals_latest', function (Blueprint $table) {
            $table->string('mas_sensor_code', 100)->primary();
            $table->unsignedBigInteger('id');
            $table->double('value');
            $table->dateTime('received_at');
            $table->enum('threshold_status', ['normal', 'watch', 'warning', 'danger', 'unknown']);
            $table->timestamp('updated_at')->nullable();

            $table->index('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_actuals_latest');
    }
};