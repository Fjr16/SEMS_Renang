<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_events', function (Blueprint $table) {
            $table->unsignedInteger('max_relay_athletes')->nullable()->after('event_type');
            $table->string('equipment', 20)->nullable()->after('max_relay_athletes');
        });
    }

    public function down(): void
    {
        Schema::table('master_events', function (Blueprint $table) {
            $table->dropColumn(['max_relay_athletes', 'equipment']);
        });
    }
};
