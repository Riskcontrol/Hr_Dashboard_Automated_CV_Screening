<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_staff_profiles_table
 * 
 * Creates the staff_profiles table in the database.
 * This table stores all employee records for the HR system.
 * Run with: php artisan migrate
 * Undo with: php artisan migrate:rollback
 */
return new class extends Migration
{
    /**
     * Run the migrations — creates the table.
     */
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // -----------------------------------------------
            // PERSONAL INFORMATION
            // -----------------------------------------------
            $table->string('full_name');                                                      // Employee's full legal name
            $table->string('employee_id')->unique();                                          // Unique ID e.g. EMP0001
            $table->enum('gender', ['male', 'female', 'other'])->nullable();                 // Gender (optional)
            $table->date('date_of_birth')->nullable();                                        // Used to calculate age
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('nationality')->nullable();                                        // e.g. Nigerian
            $table->string('profile_photo')->nullable();                                      // Path to photo file in storage

            // -----------------------------------------------
            // CONTACT DETAILS
            // -----------------------------------------------
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();                                                // Must be unique across all staff
            $table->text('residential_address')->nullable();                                  // Home address
            $table->string('emergency_contact_name')->nullable();                             // Who to call in emergency
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();                     // e.g. Spouse, Parent, Sibling

            // -----------------------------------------------
            // EMPLOYMENT DETAILS
            // -----------------------------------------------
            $table->string('job_title')->nullable();                                          // e.g. Senior Accountant
            $table->string('department')->nullable();                                         // e.g. Finance, IT, Operations
            $table->string('location')->nullable();                                           // e.g. Lagos Head Office
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->date('date_of_hire')->nullable();                                         // When they joined the company
            $table->enum('status', ['active', 'inactive', 'suspended', 'terminated'])->default('active');

            // -----------------------------------------------
            // REPORTING STRUCTURE
            // -----------------------------------------------
            $table->string('line_manager')->nullable();                                       // Direct manager's name
            $table->string('department_head')->nullable();                                    // Head of their department

            // -----------------------------------------------
            // IDENTIFICATION & COMPLIANCE
            // -----------------------------------------------
            $table->string('national_id')->nullable();                                        // NIN or Passport number
            $table->string('tax_id')->nullable();                                             // Tax Identification Number (TIN)
            $table->string('pension_details')->nullable();                                    // PFA name / PEN number

            // -----------------------------------------------
            // COMPENSATION
            // -----------------------------------------------
            $table->decimal('salary', 12, 2)->nullable();                                     // Monthly/annual salary in Naira
            $table->string('bank_name')->nullable();                                          // e.g. First Bank, GTBank
            $table->string('bank_account_number')->nullable();                                // For payroll processing

            // -----------------------------------------------
            // EDUCATION & QUALIFICATIONS
            // -----------------------------------------------
            $table->text('academic_background')->nullable();                                  // Degrees, schools, graduation years
            $table->text('certifications')->nullable();                                       // e.g. ACCA, PMP, CIPM
            $table->text('professional_memberships')->nullable();                             // e.g. ICAN, NIM

            // -----------------------------------------------
            // WORK HISTORY
            // -----------------------------------------------
            $table->text('previous_roles')->nullable();                                       // Jobs held before joining
            $table->text('promotion_history')->nullable();                                    // Promotions within this company

            // -----------------------------------------------
            // LINK TO CV APPLICATION
            // If this staff member was hired through the CV screening system,
            // this links back to their original application record.
            // nullOnDelete means if the application is deleted, this field becomes null
            // (the staff profile is NOT deleted).
            // -----------------------------------------------
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();

            $table->timestamps(); // created_at and updated_at — managed automatically by Laravel

            // Database indexes for faster search queries
            $table->index(['status', 'department']); // Used when filtering by status + department
            $table->index('employee_id');             // Used when looking up by employee ID
        });
    }

    /**
     * Reverse the migration — drops the table.
     * Called when running: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};