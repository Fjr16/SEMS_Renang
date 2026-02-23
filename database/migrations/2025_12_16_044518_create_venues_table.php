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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('address',255)->nullable(false);
            $table->string('city',100)->nullable(false);
            $table->string('province',100)->nullable();
            $table->string('country',100)->nullable(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('venues');
        Schema::enableForeignKeyConstraints();
    }
};
