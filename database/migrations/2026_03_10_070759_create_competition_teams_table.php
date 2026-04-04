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
        Schema::create('competition_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->nullable(false);
            $table->foreignId('team_id')->nullable(false);
            $table->string('status', 20)->nullable(false);
            $table->decimal('total_fee',12,2)->default(0);
            $table->string('payment_status', 20)->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_teams');
    }
};
