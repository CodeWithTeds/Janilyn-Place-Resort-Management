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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('room_type_id')->nullable()->change();
            $table->unsignedBigInteger('exclusive_resort_rental_id')->nullable()->after('room_type_id');
            $table->foreign('exclusive_resort_rental_id')->references('id')->on('exclusive_resort_rentals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['exclusive_resort_rental_id']);
            $table->dropColumn('exclusive_resort_rental_id');
            $table->unsignedBigInteger('room_type_id')->nullable(false)->change();
        });
    }
};
