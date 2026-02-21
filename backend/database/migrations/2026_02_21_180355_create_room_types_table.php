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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('min_pax')->default(1);
            $table->integer('max_pax')->default(2);
            $table->decimal('base_price_weekday', 10, 2);
            $table->decimal('base_price_weekend', 10, 2);
            $table->decimal('extra_person_charge', 10, 2)->default(0);
            $table->decimal('cooking_fee', 10, 2)->default(0);
            $table->boolean('is_package')->default(false);
            $table->text('amenities')->nullable(); // JSON or comma-separated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
