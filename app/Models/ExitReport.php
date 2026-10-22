<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ExitReport Model
 *
 * Represents the offboarding process for a staff member leaving the company.
 * Covers the exit interview, clearance checklist, and final settlement.
 *
 * Example:
 *   Staff: Naomi Nosa
 *   Exit Type: Resignation
 *   Last Working Day: 2026-07-31
 *   Clearance: IT cleared, Finance cleared, HR cleared
 *   Status: Completed
 */
class ExitReport extends Model
{
    use HasFactory;

    protected $table = 'exit_reports';

    protected $fillable = [
        // Who is leaving
        'staff_profile_id',

        // Exit details
        'exit_type',          // resignation, termination, end_of_contract, retirement, redundancy
        'resignation_date',   // When they gave notice
        'last_working_day',   // Their actual last day
        'notice_period_days', // Length of notice period

        // Exit interview
        'exit_interview_conducted', // boolean
        'exit_interview_date',
        'exit_interview_by',        // Who conducted it
        'reason_for_leaving',       // Employee's stated reason
        'feedback_company',         // Feedback about the company
        'feedback_role',            // Feedback about their role
        'would_recommend',          // Would they recommend the company (boolean)

        // Clearance checklist — each is a boolean
        'it_clearance',             // Laptop, accounts, access cards returned
        'finance_clearance',        // No outstanding loans/advances
        'hr_clearance',             // Documents handed over, ID returned
        'line_manager_clearance',   // Handover of duties completed
        'admin_clearance',          // Office items, keys returned

        // Final settlement
        'final_settlement_amount',  // Amount to be paid (could be 0)
        'settlement_status',        // pending, processed, paid
        'settlement_date',

        // Overall status
        'status',                   // in_progress, completed, cancelled

        // Notes
        'notes',
        'processed_by',             // HR officer managing this exit
    ];

    protected $casts = [
        'resignation_date'          => 'date',
        'last_working_day'          => 'date',
        'exit_interview_date'       => 'date',
        'settlement_date'           => 'date',
        'exit_interview_conducted'  => 'boolean',
        'would_recommend'           => 'boolean',
        'it_clearance'              => 'boolean',
        'finance_clearance'         => 'boolean',
        'hr_clearance'              => 'boolean',
        'line_manager_clearance'    => 'boolean',
        'admin_clearance'           => 'boolean',
        'final_settlement_amount'   => 'decimal:2',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Each exit report belongs to one staff member.
     */
    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES
    // =========================================================

    /**
     * Human-readable label for the exit type.
     */
    public function getExitTypeLabelAttribute()
    {
        return match($this->exit_type) {
            'resignation'      => 'Resignation',
            'termination'      => 'Termination',
            'end_of_contract'  => 'End of Contract',
            'retirement'       => 'Retirement',
            'redundancy'       => 'Redundancy',
            default            => ucfirst(str_replace('_', ' ', $this->exit_type)),
        };
    }

    /**
     * Colour class for the status badge.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed'   => 'badge-success',
            'in_progress' => 'badge-warning',
            'cancelled'   => 'badge-secondary',
            default       => 'badge-secondary',
        };
    }

    /**
     * Colour class for exit type badge (resignation vs termination etc).
     */
    public function getExitTypeColorAttribute()
    {
        return match($this->exit_type) {
            'resignation'      => 'badge-info',
            'termination'      => 'badge-danger',
            'redundancy'       => 'badge-danger',
            'retirement'       => 'badge-success',
            'end_of_contract'  => 'badge-secondary',
            default            => 'badge-secondary',
        };
    }

    /**
     * How many of the 5 clearance checklist items are complete.
     * Returns e.g. "3/5"
     */
    public function getClearanceProgressAttribute()
    {
        $items = [
            $this->it_clearance,
            $this->finance_clearance,
            $this->hr_clearance,
            $this->line_manager_clearance,
            $this->admin_clearance,
        ];
        $completed = count(array_filter($items));
        return $completed . '/' . count($items);
    }

    /**
     * Percentage of clearance checklist completed (for progress bars).
     */
    public function getClearancePercentageAttribute()
    {
        $items = [
            $this->it_clearance,
            $this->finance_clearance,
            $this->hr_clearance,
            $this->line_manager_clearance,
            $this->admin_clearance,
        ];
        $completed = count(array_filter($items));
        return round(($completed / count($items)) * 100);
    }

    /**
     * Whether ALL clearance items are complete.
     */
    public function getIsFullyClearedAttribute()
    {
        return $this->it_clearance
            && $this->finance_clearance
            && $this->hr_clearance
            && $this->line_manager_clearance
            && $this->admin_clearance;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /** Only in-progress exits */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /** Filter by exit type */
    public function scopeOfType($query, $type)
    {
        return $query->where('exit_type', $type);
    }

    /** Exits where clearance is not yet fully complete */
    public function scopePendingClearance($query)
    {
        return $query->where(function ($q) {
            $q->where('it_clearance', false)
              ->orWhere('finance_clearance', false)
              ->orWhere('hr_clearance', false)
              ->orWhere('line_manager_clearance', false)
              ->orWhere('admin_clearance', false);
        });
    }
}