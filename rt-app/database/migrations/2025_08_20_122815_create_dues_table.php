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
        Schema::create('dues', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month'); // 1..12
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('amount');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate dues for same household/month/year
            $table->unique(['household_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues');
    }
};
