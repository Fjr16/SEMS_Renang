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
        Schema::create('competition_heat_lanes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_heat_id')->nullable(false);
            $table->foreignId('competition_entry_id')->nullable(false);
            $table->unsignedInteger('lane_number')->nullable(false);
            $table->unsignedInteger('lane_order')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_heat_lanes');
    }
};
