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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable(false);
            $table->foreignId('venue_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('code', 50)->nullable(false);
            $table->string('description')->nullable(true);
            $table->date('start_date')->nullable(false);
            $table->date('end_date')->nullable(false);
            $table->date('registration_start')->nullable(false);
            $table->date('registration_end')->nullable(false);
            $table->string('sanction_number')->nullable(true);
            $table->string('status',50)->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
