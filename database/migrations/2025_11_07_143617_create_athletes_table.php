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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->nullable(false);
            $table->string('code')->nullable(false)->unique();
            $table->string('foto')->nullable(true);
            $table->string('name')->nullable(false);
            $table->date('bod')->nullable(false);
            $table->string('gender', 50)->nullable(false);
            $table->string('registration_number')->nullable(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
