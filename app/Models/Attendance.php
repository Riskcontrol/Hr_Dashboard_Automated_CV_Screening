<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Attendance Model
 *
 * Represents a single attendance or absence record for a staff member.
 * Each record covers one day and tracks whether the staff came in,
 * was absent, late, on leave, etc.
 *
 * Example:
 *   Staff: Naomi Nosa
 *   Date: 2026-06-13
 *   Status: absent
 *   Absence Type: sick
 *   Notes: Called in sick, doctor's note submitted
 */
class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        // Who this record is for
        'staff_profile_id',

        // The date this record covers
        'date',

        // Attendance status for the day
        'status',           // present, absent, late, half_day, on_leave, public_holiday

        // If absent — what type of absence
        'absence_type',     // sick, unauthorised, personal, bereavement, maternity, other

        // Time tracking (optional)
        'check_in_time',    // e.g. 09:15
        'check_out_time',   // e.g. 17:30
        'minutes_late',     // How many minutes late (if status = late)

        // Notes & approval
        'notes',            // Any additional context from HR
        'recorded_by',      // Name of HR officer who entered this record
    ];

    protected $casts = [
        'date'          => 'date',
        'minutes_late'  => 'integer',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Each attendance record belongs to one staff member.
     */
    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES
    // =========================================================

    /**
     * Human-readable label for the attendance status.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present'        => 'Present',
            'absent'         => 'Absent',
            'late'           => 'Late',
            'half_day'       => 'Half Day',
            'on_leave'       => 'On Leave',
            'public_holiday' => 'Public Holiday',
            default          => ucfirst($this->status),
        };
    }

    /**
     * Colour class for the status badge.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present'        => 'badge-success',
            'absent'         => 'badge-danger',
            'late'           => 'badge-warning',
            'half_day'       => 'badge-info',
            'on_leave'       => 'badge-secondary',
            'public_holiday' => 'badge-secondary',
            default          => 'badge-secondary',
        };
    }

    /**
     * Human-readable label for the absence type.
     */
    public function getAbsenceTypeLabelAttribute()
    {
        if (!$this->absence_type) return null;
        return match($this->absence_type) {
            'sick'          => 'Sick Leave',
            'unauthorised'  => 'Unauthorised Absence',
            'personal'      => 'Personal Reasons',
            'bereavement'   => 'Bereavement',
            'maternity'     => 'Maternity/Paternity',
            'other'         => 'Other',
            default         => ucfirst($this->absence_type),
        };
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /** Only absent records */
    public function scopeAbsences($query)
    {
        return $query->where('status', 'absent');
    }

    /** Only late records */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /** Filter by date range */
    public function scopeInRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /** Filter by staff member */
    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_profile_id', $staffId);
    }

    /** Unauthorised absences only */
    public function scopeUnauthorised($query)
    {
        return $query->where('status', 'absent')
                     ->where('absence_type', 'unauthorised');
    }
}