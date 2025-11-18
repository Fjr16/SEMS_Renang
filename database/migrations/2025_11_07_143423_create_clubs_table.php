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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_role_category_id');
            $table->string('club_code')->nullable(false);
            $table->string('club_name')->nullable(false);
            $table->string('club_logo')->nullable();
            $table->string('club_address')->nullable();
            $table->string('club_province')->nullable();
            $table->string('club_lead')->nullable();
            $table->string('lead_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
