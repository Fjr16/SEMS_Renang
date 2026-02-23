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
            $table->string('club_name')->nullable(false);
            $table->string('club_code')->nullable(false);
            $table->string('club_city')->nullable(true);
            $table->string('club_province')->nullable(true);
            $table->string('club_lead')->nullable(true);
            $table->string('lead_phone')->nullable(true);
            $table->enum('team_type', ['school', 'club', 'city', 'province', 'nation'])->nullable(false);
            $table->string('club_logo')->nullable(true);
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
