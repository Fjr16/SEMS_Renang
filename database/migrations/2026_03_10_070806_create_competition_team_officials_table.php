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
        Schema::create('competition_team_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_team_id')->nullable(false);
            $table->foreignId('official_id')->nullable(false);
            $table->string('role_override', 50)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_team_officials');
    }
};
