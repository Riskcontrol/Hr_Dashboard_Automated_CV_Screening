<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_leave_requests_table
 *
 * Creates the leave_requests table.
 * Run with: php artisan migrate
 * Undo with: php artisan migrate:rollback
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            // -----------------------------------------------
            // WHICH STAFF MEMBER IS THIS LEAVE FOR?
            // If the staff profile is deleted, this record stays
            // (nullOnDelete keeps historical data intact)
            // -----------------------------------------------
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete(); // Delete leave records if staff is deleted

            // -----------------------------------------------
            // LEAVE DETAILS
            // -----------------------------------------------
            $table->enum('leave_type', [
                'annual',      // Standard paid annual leave
                'sick',        // Medical/sick leave
                'casual',      // Short casual leave
                'maternity',   // Maternity leave
                'paternity',   // Paternity leave
                'unpaid',      // Unpaid leave
            ]);

            $table->date('start_date');                         // First day of leave
            $table->date('end_date');                           // Last day of leave
            $table->unsignedInteger('total_days')->default(1); // Auto-calculated working days
            $table->text('reason')->nullable();                 // Why they need leave

            // -----------------------------------------------
            // APPROVER DETAILS
            // HR selects who should approve from a dropdown:
            // either the staff member's line manager or HR directly
            // -----------------------------------------------
            $table->enum('approver_type', ['line_manager', 'hr']);
            $table->string('approver_name');                    // Actual name of the chosen approver

            // -----------------------------------------------
            // STATUS TRACKING
            // -----------------------------------------------
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('approved_by')->nullable();          // Who approved/rejected it
            $table->timestamp('approved_at')->nullable();       // When it was actioned
            $table->text('rejection_reason')->nullable();       // Reason if rejected

            // -----------------------------------------------
            // WHO SUBMITTED THIS REQUEST?
            // 'hr'    = HR submitted it on behalf of staff
            // 'staff' = Staff submitted it themselves
            // -----------------------------------------------
            $table->enum('submitted_by', ['hr', 'staff'])->default('hr');

            $table->timestamps(); // created_at and updated_at

            // Indexes for faster queries
            $table->index(['staff_profile_id', 'status']);     // Filter by staff + status
            $table->index(['start_date', 'end_date']);          // Date range queries
            $table->index('status');                            // Filter by status alone
        });
    }

    /**
     * Reverse the migration — drops the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};