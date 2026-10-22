<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_compliance_records_table
 *
 * Creates the compliance_records table for tracking
 * staff certifications, licenses, and document expiry dates.
 *
 * IMPORTANT — Migration order:
 * This file's timestamp MUST be AFTER staff_profiles and
 * AFTER leave_requests/kpis (since it references staff_profiles).
 * If you renamed earlier migrations, name this one LATER
 * e.g. 2026_06_02_054854_create_compliance_records_table.php
 *
 * Run with: php artisan migrate
 * Undo with: php artisan migrate:rollback
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_records', function (Blueprint $table) {
            $table->id();

            // -----------------------------------------------
            // WHICH STAFF MEMBER DOES THIS DOCUMENT BELONG TO?
            // Deleting a staff profile also deletes their compliance records.
            // -----------------------------------------------
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            // -----------------------------------------------
            // DOCUMENT DETAILS
            // -----------------------------------------------
            $table->enum('document_type', [
                'certification',     // e.g. ACCA, PMP, CIPM
                'license',           // e.g. Driver's license, professional license
                'insurance',         // e.g. Professional indemnity insurance
                'contract',          // e.g. Employment contract renewal
                'id_document',       // e.g. National ID, Passport
                'medical',           // e.g. Medical fitness certificate
                'background_check',  // e.g. Police clearance, reference check
            ]);

            $table->string('document_name');                   // e.g. "ACCA Certificate"
            $table->string('document_number')->nullable();     // Reference/serial number
            $table->string('issuing_body')->nullable();         // e.g. "ICAN", "FRSC"

            // -----------------------------------------------
            // DATES
            // expiry_date nullable because some documents
            // (e.g. National ID in some cases) never expire
            // -----------------------------------------------
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // -----------------------------------------------
            // FILE UPLOAD
            // Path to scanned copy of the document
            // -----------------------------------------------
            $table->string('document_file')->nullable();

            // -----------------------------------------------
            // STATUS
            // 'valid' and 'renewal_in_progress' are set manually by HR.
            // 'expiring_soon' and 'expired' are CALCULATED automatically
            // from expiry_date (see ComplianceRecord::getComputedStatusAttribute)
            // and are NOT stored directly — only 'valid' or 'renewal_in_progress'
            // are ever saved to this column.
            // -----------------------------------------------
            $table->enum('status', ['valid', 'renewal_in_progress'])->default('valid');

            // -----------------------------------------------
            // NOTES & REMINDERS
            // -----------------------------------------------
            $table->text('notes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable(); // For future email reminder feature

            $table->timestamps();

            // Indexes for faster queries
            $table->index('staff_profile_id');
            $table->index('expiry_date');   // Used for expiry alerts
            $table->index('document_type'); // Filter by type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_records');
    }
};