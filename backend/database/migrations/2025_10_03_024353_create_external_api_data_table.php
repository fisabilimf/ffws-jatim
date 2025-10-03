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
        Schema::create('external_api_data', function (Blueprint $table) {
            $table->id();
            
            // External API identifiers
            $table->bigInteger('external_id')->index(); // ID from external API
            $table->string('judul')->nullable(); // Station title/name
            $table->string('kode')->nullable()->index(); // Station code
            $table->string('tipe_input')->nullable(); // Input type (jam, hari, etc.)
            $table->string('alamat')->nullable(); // Address/location
            
            // Geographic coordinates
            $table->decimal('longitude', 11, 8)->nullable()->index();
            $table->decimal('latitude', 10, 8)->nullable()->index();
            
            // Temporal data
            $table->date('tanggal')->nullable()->index(); // Date
            $table->integer('jam')->nullable(); // Hour (0-23)
            
            // Measurement data
            $table->decimal('value', 8, 3)->nullable(); // Measured value
            $table->string('label')->nullable(); // Status label
            $table->string('icon')->nullable(); // Icon reference
            $table->string('warna')->nullable(); // Color code
            
            // API metadata
            $table->enum('api_source', [
                'cuaca-arr-pusda',
                'meteorologi-juanda', 
                'cuaca-awlr-pusda'
            ])->index();
            $table->enum('parameter_type', [
                'rainfall',
                'water-level'
            ])->index();
            
            // Raw data and sync tracking
            $table->json('raw_data')->nullable(); // Complete API response
            $table->timestamp('created_at_source')->nullable(); // Created at from API
            $table->timestamp('updated_at_source')->nullable(); // Updated at from API
            $table->enum('sync_status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('last_sync_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['api_source', 'external_id']);
            $table->index(['tanggal', 'jam']);
            $table->index(['parameter_type', 'tanggal']);
            $table->index(['kode', 'tanggal', 'jam']);
            $table->index(['longitude', 'latitude']);
            
            // Unique constraint to prevent duplicates
            $table->unique(['external_id', 'api_source'], 'unique_external_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_api_data');
    }
};
