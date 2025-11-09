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
            $table->foreignId('club_id')->nullable();
            $table->string('code')->nullable();
            $table->string('foto')->nullable();
            $table->string('name')->nullable();
            $table->date('bod')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('school_name')->nullable();
            $table->string('club_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('province_name')->nullable();
            // $table->string('prsi_id')->nullable();
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
