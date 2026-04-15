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
        Schema::create('competition_heats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_event_id')->nullable(false);
            $table->string('round_type', 30)->nullable(false);
            $table->unsignedInteger('heat_number')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_heats');
    }
};
