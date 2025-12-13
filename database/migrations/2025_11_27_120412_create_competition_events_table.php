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
        Schema::create('competition_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->nullable(false);
            $table->foreignId('competition_session_id')->nullable(false);
            $table->foreignId('age_group_id')->nullable(false);
            $table->string('event_number', 16)->nullable(false);
            $table->unsignedInteger('distance')->nullable(false);
            $table->string('stroke',50)->nullable(false);
            $table->string('gender',10)->nullable(false);
            $table->string('event_type',20)->nullable(false);
            $table->string('event_system',20)->nullable(false);
            $table->text('remarks')->nullable(true);
            // $table->date('min_dob')->nullable(true);
            // $table->date('max_dob')->nullable(true);
            $table->decimal('registration_fee',10,2)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_events');
    }
};
