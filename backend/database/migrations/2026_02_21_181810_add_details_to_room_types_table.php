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
        Schema::table('room_types', function (Blueprint $table) {
            $table->integer('bedroom_count')->nullable()->after('max_pax');
            $table->integer('max_day_guests')->nullable()->after('bedroom_count')->comment('For day guests until 10:00pm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['bedroom_count', 'max_day_guests']);
        });
    }
};
