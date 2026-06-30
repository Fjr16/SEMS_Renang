<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_events', function (Blueprint $table) {
            $table->dropUnique('unique_master_event');
            $table->unique(['distance', 'stroke', 'gender', 'age_group_id', 'event_type', 'equipment'], 'unique_master_event');
        });
    }

    public function down(): void
    {
        Schema::table('master_events', function (Blueprint $table) {
            $table->dropUnique('unique_master_event');
            $table->unique(['distance', 'stroke', 'gender', 'age_group_id', 'event_type'], 'unique_master_event');
        });
    }
};
