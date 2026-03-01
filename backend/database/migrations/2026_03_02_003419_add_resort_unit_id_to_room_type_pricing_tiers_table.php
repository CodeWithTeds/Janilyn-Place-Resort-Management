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
        Schema::table('room_type_pricing_tiers', function (Blueprint $table) {
            $table->foreignId('resort_unit_id')->nullable()->constrained()->onDelete('cascade');
            // Make room_type_id nullable if a tier can be unit-specific only (though usually unit belongs to room type)
            // But we can keep room_type_id required if we consider that a unit always has a room type.
            // However, to allow "Unit Specific" tiers, we might want to relax room_type_id constraint if it's strictly bound to unit?
            // Actually, better to keep room_type_id as context, or make it nullable if we want pure unit-based tiers.
            // Let's make room_type_id nullable in a separate migration if needed, but for now just adding resort_unit_id is enough.
            // Wait, if I add resort_unit_id, it means this tier applies to this unit.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_type_pricing_tiers', function (Blueprint $table) {
            $table->dropForeign(['resort_unit_id']);
            $table->dropColumn('resort_unit_id');
        });
    }
};
