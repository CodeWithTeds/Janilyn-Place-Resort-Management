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
        // Staff Schedules Table
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('type')->default('shift'); // shift, leave, meeting, etc.
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->timestamps();
        });

        // Staff Attendance Table
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->string('status')->default('present'); // present, absent, late, leave
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Staff Performance Table
        Schema::create('staff_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('rating'); // 1-5
            $table->text('feedback')->nullable();
            $table->date('review_date');
            $table->timestamps();
        });

        // Staff Tasks Table (Generic)
        Schema::create('staff_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_tasks');
        Schema::dropIfExists('staff_performance');
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('staff_schedules');
    }
};
