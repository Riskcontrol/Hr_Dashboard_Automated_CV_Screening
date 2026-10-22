<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_exit_reports_table
 *
 * Creates the exit_reports table for tracking staff offboarding:
 * exit interviews, clearance checklists, and final settlement.
 *
 * Filename timestamp MUST be after attendance (054856):
 * 2026_06_02_054857_create_exit_reports_table.php
 *
 * Run with: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exit_reports', function (Blueprint $table) {
            $table->id();

            // Which staff member is leaving
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            // -----------------------------------------------
            // EXIT DETAILS
            // -----------------------------------------------
            $table->enum('exit_type', [
                'resignation',      // Staff chose to leave
                'termination',      // Company terminated employment
                'end_of_contract',  // Fixed-term contract ended
                'retirement',       // Staff retired
                'redundancy',       // Position made redundant
            ]);

            $table->date('resignation_date')->nullable();   // When notice was given
            $table->date('last_working_day');                // Actual last day at work
            $table->unsignedInteger('notice_period_days')->nullable(); // Length of notice

            // -----------------------------------------------
            // EXIT INTERVIEW
            // -----------------------------------------------
            $table->boolean('exit_interview_conducted')->default(false);
            $table->date('exit_interview_date')->nullable();
            $table->string('exit_interview_by')->nullable();  // Who conducted the interview
            $table->text('reason_for_leaving')->nullable();   // Employee's stated reason
            $table->text('feedback_company')->nullable();     // Feedback about the company
            $table->text('feedback_role')->nullable();        // Feedback about their role
            $table->boolean('would_recommend')->default(false); // Would recommend company to others

            // -----------------------------------------------
            // CLEARANCE CHECKLIST
            // Each department confirms the employee is cleared
            // -----------------------------------------------
            $table->boolean('it_clearance')->default(false);             // Laptop, accounts, access returned
            $table->boolean('finance_clearance')->default(false);        // No outstanding loans/advances
            $table->boolean('hr_clearance')->default(false);             // Documents handed over
            $table->boolean('line_manager_clearance')->default(false);   // Duties handed over
            $table->boolean('admin_clearance')->default(false);          // Office items, keys returned

            // -----------------------------------------------
            // FINAL SETTLEMENT
            // -----------------------------------------------
            $table->decimal('final_settlement_amount', 12, 2)->nullable(); // Amount owed to employee
            $table->enum('settlement_status', ['pending', 'processed', 'paid'])->default('pending');
            $table->date('settlement_date')->nullable();

            // -----------------------------------------------
            // OVERALL STATUS
            // -----------------------------------------------
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');

            // -----------------------------------------------
            // NOTES
            // -----------------------------------------------
            $table->text('notes')->nullable();
            $table->string('processed_by')->nullable(); // HR officer managing this exit

            $table->timestamps();

            // Indexes for faster filtering
            $table->index(['staff_profile_id', 'status']);
            $table->index('last_working_day'); // For reports by date
            $table->index('exit_type');         // Filter by exit type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_reports');
    }
};