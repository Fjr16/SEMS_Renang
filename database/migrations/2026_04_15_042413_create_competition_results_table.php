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
        Schema::create('competition_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_heat_lane_id')->nullable(false);
            $table->string('reaction_time',20)->nullable(true);
            $table->string('swim_time',20)->nullable(true);
            $table->string('status',20)->nullable(false);
            $table->unsignedInteger('rank_in_heat')->nullable(false);
            $table->unsignedInteger('rank_overral')->nullable(false);
            $table->decimal('points',10,2)->nullable(false);
            $table->string('record_type',20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_results');
    }
};
