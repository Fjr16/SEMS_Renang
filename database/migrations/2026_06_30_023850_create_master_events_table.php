<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('distance');
            $table->string('stroke', 50);
            $table->string('gender', 10);
            $table->foreignId('age_group_id')->constrained('age_groups');
            $table->string('event_type', 20);
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['distance', 'stroke', 'gender', 'age_group_id', 'event_type', 'equipment'], 'unique_master_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_events');
    }
};
