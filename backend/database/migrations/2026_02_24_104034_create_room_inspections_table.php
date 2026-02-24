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
        Schema::create('room_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['Pre-Arrival', 'Post-Departure', 'Routine'])->default('Routine');
            $table->enum('status', ['Passed', 'Failed', 'Pending Fix'])->default('Passed');
            $table->json('checklist_data')->nullable(); // Stores checklist items and their status
            $table->text('notes')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_inspections');
    }
};
