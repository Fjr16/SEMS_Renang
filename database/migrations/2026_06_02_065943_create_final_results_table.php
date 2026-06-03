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
        Schema::create('final_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->nullable(false);
            $table->foreignId('competition_event_id')->nullable(false);
            $table->foreignId('competition_entry_id')->nullable(false);
            $table->foreignId('competition_heat_lane_id')->nullable(false);
            $table->boolean('is_relay')->default(false);
            $table->foreignId('athlete_id')->nullable(true);
            $table->foreignId('competition_team_id')->nullable(true);
            $table->string('entry_time', 20)->nullable(true);
            $table->string('swim_time', 20)->nullable(true);
            $table->integer('rank_in_event')->nullable(true);
            $table->string('status')->nullable(false);
            $table->string('round_type')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_results');
    }
};
