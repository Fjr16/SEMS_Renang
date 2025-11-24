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
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->nullable();
            $table->string('foto')->nullable();
            $table->string('name')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('license', 50)->nullable();
            // $table->string('current_club')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_province')->nullable();
            // $table->string('certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
