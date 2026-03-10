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
            $table->enum('status',['active','withdrawn','disqualified'])->default('active');
            $table->decimal('total_fee',12,2)->default(0);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
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
