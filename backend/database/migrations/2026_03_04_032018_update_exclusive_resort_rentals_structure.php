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
        // Drop table if exists to handle failed migration cleanup
        Schema::dropIfExists('exclusive_resort_rental_pricing_tiers');

        // 1. Create the pricing tiers table
        Schema::create('exclusive_resort_rental_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exclusive_resort_rental_id')
                  ->constrained('exclusive_resort_rentals', 'id', 'ex_resort_rental_tiers_rental_id_fk') // Shortened index name
                  ->cascadeOnDelete();
            $table->integer('min_guests');
            $table->integer('max_guests');
            $table->decimal('price_weekday', 10, 2);
            $table->decimal('price_weekend', 10, 2);
            $table->timestamps();
        });

        // 2. Modify the main table
        Schema::table('exclusive_resort_rentals', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'price_range_min',
                'price_range_max',
                'capacity_overnight_min',
                'capacity_overnight_max',
                'capacity_day_min',
                'capacity_day_max',
            ]);

            // Add new columns to match RoomType structure
            $table->integer('min_pax')->default(0);
            $table->integer('max_pax')->default(0);
            $table->decimal('base_price_weekday', 10, 2)->default(0);
            $table->decimal('base_price_weekend', 10, 2)->default(0);
            $table->decimal('extra_person_charge', 10, 2)->default(0);
            // cooking_fee already exists
            // features already exists
            // image_path already exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exclusive_resort_rentals', function (Blueprint $table) {
            $table->decimal('price_range_min', 10, 2)->nullable();
            $table->decimal('price_range_max', 10, 2)->nullable();
            $table->integer('capacity_overnight_min')->nullable();
            $table->integer('capacity_overnight_max')->nullable();
            $table->integer('capacity_day_min')->nullable();
            $table->integer('capacity_day_max')->nullable();

            $table->dropColumn([
                'min_pax',
                'max_pax',
                'base_price_weekday',
                'base_price_weekend',
                'extra_person_charge',
            ]);
        });

        Schema::dropIfExists('exclusive_resort_rental_pricing_tiers');
    }
};
