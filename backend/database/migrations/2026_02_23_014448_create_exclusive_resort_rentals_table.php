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
        Schema::create('exclusive_resort_rentals', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Resort Rental + 3 Apartments
            $table->text('description')->nullable(); // e.g., (6 Bedrooms)
            $table->decimal('price_range_min', 10, 2); // e.g., 10000
            $table->decimal('price_range_max', 10, 2); // e.g., 12000
            $table->integer('capacity_overnight_min')->nullable(); // e.g., 20
            $table->integer('capacity_overnight_max')->nullable(); // e.g., 30
            $table->integer('capacity_day_min')->nullable(); // e.g., 30
            $table->integer('capacity_day_max')->nullable(); // e.g., 60
            $table->decimal('cooking_fee', 8, 2)->default(300.00); // e.g., 300
            $table->json('features')->nullable(); // Additional features
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclusive_resort_rentals');
    }
};
