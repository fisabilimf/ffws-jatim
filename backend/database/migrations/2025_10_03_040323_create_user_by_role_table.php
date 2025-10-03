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
        Schema::create('user_by_role', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 30)->nullable();
            $table->string('upt_code', 100)->nullable()->unique();
            $table->enum('role', ['admin', 'user', 'moderator'])->default('user');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->foreign('upt_code', 'fk_ubr_upt_code')
                  ->references('upts_code')
                  ->on('mas_upts')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_by_role');
    }
};