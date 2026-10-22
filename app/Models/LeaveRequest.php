<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_profile_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'approver_type',
        'approver_name',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'submitted_by',
        'hr_notes',
        'staff_section',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    public function getLeaveTypeLabelAttribute()
    {
        return match($this->leave_type) {
            'annual'    => 'Annual Leave',
            'sick'      => 'Sick Leave',
            'casual'    => 'Casual Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'unpaid'    => 'Unpaid Leave',
            default     => ucfirst($this->leave_type),
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default    => 'badge-warning',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_profile_id', $staffId);
    }

    public static function calculateWorkingDays($startDate, $endDate): int
    {
        $start   = \Carbon\Carbon::parse($startDate);
        $end     = \Carbon\Carbon::parse($endDate);
        $days    = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }
}