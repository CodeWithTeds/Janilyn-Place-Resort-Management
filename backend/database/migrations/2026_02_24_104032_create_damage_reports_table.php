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
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resort_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->string('item_name');
            $table->text('description');
            $table->enum('severity', ['Minor', 'Moderate', 'Severe'])->default('Minor');
            $table->enum('status', ['Pending', 'In Progress', 'Resolved', 'Written Off'])->default('Pending');
            $table->decimal('cost_estimate', 10, 2)->nullable();
            $table->json('images')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
