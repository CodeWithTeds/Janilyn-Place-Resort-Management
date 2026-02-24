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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('General'); // Linen, Toiletries, Cleaning Supplies, etc.
            $table->integer('quantity')->default(0);
            $table->string('unit')->default('pcs'); // pcs, box, set, kg, etc.
            $table->integer('reorder_level')->default(10);
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->string('location')->nullable(); // Storage Room A, Laundry, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
