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
        Schema::create('competition_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->nullable(false);
            $table->foreignId('pool_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->integer('session_order')->nullable(false);
            $table->date('session_date')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_sessions');
    }
};
