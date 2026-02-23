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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->string('code', 50)->unique()->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('pool_role',100)->nullable(false);
            $table->enum('course_type', ['SCM','LCM','SCY'])->nullable(false); //SCM = 25m/LCM = 50m/SCY = 25yd
            $table->integer('length_meter',false,true)->nullable(false);
            $table->smallInteger('total_lanes',false,true)->nullable(false);
            $table->unsignedBigInteger('depth')->nullable(false);
            $table->enum('status', ['active','inactive'])->default('active');
            // $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pools');
    }
};
