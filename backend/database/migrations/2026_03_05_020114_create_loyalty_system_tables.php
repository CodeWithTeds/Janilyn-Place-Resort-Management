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
        // Add loyalty columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('profile_photo_path');
            $table->string('loyalty_tier')->default('Bronze')->after('loyalty_points');
            $table->text('guest_notes')->nullable()->after('loyalty_tier');
        });

        // Create loyalty rewards table (Owner defines these)
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create loyalty transactions table (Points earned/spent)
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // Positive for earned, negative for spent
            $table->string('type'); // 'earned', 'redeemed', 'adjustment', 'bonus'
            $table->string('description')->nullable();
            $table->nullableMorphs('reference'); // e.g., Booking model
            $table->timestamps();
        });

        // Create user rewards table (Rewards claimed by users)
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_reward_id')->constrained()->cascadeOnDelete();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'used', 'expired'
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_rewards');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'loyalty_tier', 'guest_notes']);
        });
    }
};
