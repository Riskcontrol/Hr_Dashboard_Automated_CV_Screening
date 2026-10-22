<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_attendance_table
 *
 * Creates the attendance table for tracking daily staff
 * attendance and absence records.
 *
 * Filename timestamp MUST be after appraisals (054855):
 * 2026_06_02_054856_create_attendance_table.php
 *
 * Run with: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();

            // Which staff member this record is for
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            // The date this record covers — one record per staff per day
            $table->date('date');

            // -----------------------------------------------
            // ATTENDANCE STATUS
            // -----------------------------------------------
            $table->enum('status', [
                'present',         // Staff came in
                'absent',          // Staff did not come in
                'late',            // Staff came in but late
                'half_day',        // Staff worked half the day
                'on_leave',        // Staff is on approved leave
                'public_holiday',  // Public holiday — no attendance expected
            ]);

            // -----------------------------------------------
            // ABSENCE DETAILS
            // Only filled when status = 'absent'
            // -----------------------------------------------
            $table->enum('absence_type', [
                'sick',            // Sick leave
                'unauthorised',    // No reason given / not approved
                'personal',        // Personal reasons
                'bereavement',     // Death in family
                'maternity',       // Maternity or paternity
                'other',           // Any other reason
            ])->nullable();

            // -----------------------------------------------
            // TIME TRACKING (optional)
            // -----------------------------------------------
            $table->time('check_in_time')->nullable();   // e.g. 09:15
            $table->time('check_out_time')->nullable();  // e.g. 17:30
            $table->integer('minutes_late')->nullable(); // e.g. 15 (for late arrivals)

            // -----------------------------------------------
            // NOTES & WHO RECORDED IT
            // -----------------------------------------------
            $table->text('notes')->nullable();           // Context or explanation
            $table->string('recorded_by')->nullable();   // HR officer who entered this

            $table->timestamps();

            // Prevent duplicate records for the same staff on the same day
            $table->unique(['staff_profile_id', 'date']);

            // Indexes for faster filtering
            $table->index('date');
            $table->index('status');
            $table->index(['staff_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};