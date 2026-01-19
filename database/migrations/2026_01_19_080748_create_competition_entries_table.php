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
            $table->foreignId('athlete_id')->unsigned()->nullable(false);
            $table->foreignId('competition_event_id')->unsigned()->nullable(false);
            $table->decimal('seed_time',8,2)->nullable(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('feedback')->nullable(true);
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
