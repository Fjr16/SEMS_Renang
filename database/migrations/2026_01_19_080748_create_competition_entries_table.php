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
        Schema::create('competition_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_team_id')->unsigned()->nullable(false);
            $table->foreignId('competition_event_id')->unsigned()->nullable(false);
            $table->foreignId('athlete_id')->unsigned()->nullable(true);
            $table->boolean('is_relay')->nullable(false);
            $table->string('entry_time',20)->nullable(true);
            $table->string('seed_time',20)->nullable(true);
            $table->enum('status', ['entered', 'confirmed', 'scratched'])->default('entered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_entries');
    }
};
