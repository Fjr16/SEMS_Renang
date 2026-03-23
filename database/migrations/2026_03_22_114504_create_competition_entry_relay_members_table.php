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
        Schema::create('competition_entry_relay_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_entry_id')->unsigned()->nullable(false);
            $table->foreignId('athlete_id')->unsigned()->nullable(false);
            $table->integer('leg_order')->unsigned()->nullable(false);
            $table->enum('status', ['active', 'scratched'])->nullable(false)->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_entry_relay_members');
    }
};
