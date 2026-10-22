<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_appraisals_table
 *
 * Creates the appraisals table for tracking probation reviews
 * and performance appraisals.
 *
 * Filename timestamp MUST be after compliance (054854):
 * 2026_06_02_054855_create_appraisals_table.php
 *
 * Run with: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();

            // Link to the staff member being appraised
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            // -----------------------------------------------
            // TYPE OF APPRAISAL
            // -----------------------------------------------
            $table->enum('appraisal_type', [
                'probation_3month', // 3-month probation check-in
                'probation_6month', // 6-month end-of-probation review
                'annual',           // Yearly performance appraisal
                'mid_year',         // Mid-year check-in
                'pip',              // Performance Improvement Plan
            ]);

            // -----------------------------------------------
            // DATES
            // -----------------------------------------------
            $table->date('due_date');                          // When appraisal must happen
            $table->date('completed_date')->nullable();        // When it was actually done

            // -----------------------------------------------
            // REVIEWER
            // -----------------------------------------------
            $table->string('reviewer_name');                   // e.g. "John Doe"
            $table->string('reviewer_role')->nullable();       // e.g. "Line Manager", "HR Manager"

            // -----------------------------------------------
            // OUTCOME
            // -----------------------------------------------
            $table->enum('overall_rating', [
                'excellent',
                'good',
                'satisfactory',
                'needs_improvement',
                'unsatisfactory',
            ])->nullable(); // Nullable until appraisal is completed

            $table->enum('status', [
                'pending',      // Scheduled but not started
                'in_progress',  // Currently being conducted
                'completed',    // Finished
                'cancelled',    // Cancelled
            ])->default('pending');

            // Probation-specific outcome — only relevant for probation types
            $table->enum('probation_outcome', [
                'confirmed',   // Staff confirmed as permanent employee
                'extended',    // Probation extended for further review
                'terminated',  // Employment terminated
            ])->nullable();

            // -----------------------------------------------
            // EVALUATION FORM FIELDS
            // These are filled in by the reviewer during the appraisal
            // -----------------------------------------------
            $table->text('performance_summary')->nullable();    // Overall summary
            $table->text('strengths')->nullable();              // What the employee does well
            $table->text('areas_for_improvement')->nullable();  // Areas needing development
            $table->text('goals_next_period')->nullable();      // Goals for next period
            $table->text('employee_comments')->nullable();      // Employee's own comments
            $table->text('reviewer_comments')->nullable();      // Reviewer's final remarks

            // -----------------------------------------------
            // REMINDER TRACKING
            // For future automated email reminder feature
            // -----------------------------------------------
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();

            // Indexes for faster queries
            $table->index(['staff_profile_id', 'status']);
            $table->index('due_date');        // For upcoming appraisal alerts
            $table->index('appraisal_type');  // Filter by type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};