<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    protected $table = 'appraisals';

    protected $fillable = [
        'staff_profile_id',
        'appraisal_type',
        'appraisal_year',
        'form_type',
        'due_date',
        'completed_date',
        'reviewer_name',
        'reviewer_role',
        'overall_rating',
        'status',
        'probation_outcome',
        'performance_summary',
        'strengths',
        'areas_for_improvement',
        'goals_next_period',
        'employee_comments',
        'reviewer_comments',
        'reminder_sent_at',
        'sent_to_employee_at',
        'self_mission_statement',
        'self_duties_understanding',
        'self_job_achievements',
        'self_other_achievements',
        'self_likes_dislikes',
        'self_most_difficult',
        'self_improvement_actions',
        'prob_professionalism','prob_professionalism_comments',
        'prob_crisis_management','prob_crisis_management_comments',
        'prob_quality_of_work','prob_quality_of_work_comments',
        'prob_dependability','prob_dependability_comments',
        'prob_team_spirit','prob_team_spirit_comments',
        'prob_result_orientation','prob_result_orientation_comments',
        'prob_followership','prob_followership_comments',
        'prob_self_discipline','prob_self_discipline_comments',
        'prob_organisation_planning','prob_organisation_planning_comments',
        'prob_self_development','prob_self_development_comments',
        'prob_skill_deficiencies',
        'prob_constraints',
        'prob_appraisee_comments',
        'prob_observer_comments',
        'prob_hod_comments',
        'edit_history',
    ];

    protected $casts = [
        'due_date'            => 'date',
        'completed_date'      => 'date',
        'reminder_sent_at'    => 'datetime',
        'sent_to_employee_at' => 'datetime',
        'edit_history'        => 'array',
    ];

    /**
     * Append an entry to the edit history log.
     * Called any time the form is saved.
     */
    public function recordEdit(string $editedBy, array $changed = []): void
    {
        $history   = $this->edit_history ?? [];
        $history[] = [
            'edited_by' => $editedBy,
            'timestamp' => now()->toDateTimeString(),
            'changes'   => $changed,
        ];
        $this->edit_history = $history;
    }

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES
    // =========================================================

    public function getAppraisalTypeLabelAttribute()
    {
        return match($this->appraisal_type) {
            'probation_3month' => 'Probation Review (3 Months)',
            'probation_6month' => 'Probation Review (6 Months)',
            'annual'           => 'Annual Appraisal',
            'mid_year'         => 'Mid-Year Review',
            'pip'              => 'Performance Improvement Plan',
            default            => ucfirst(str_replace('_', ' ', $this->appraisal_type)),
        };
    }

    public function getIsProbationAttribute()
    {
        return in_array($this->appraisal_type, ['probation_3month', 'probation_6month']);
    }

    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) return null;
        return now()->startOfDay()->diffInDays($this->due_date, false);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'completed'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function getRatingColorAttribute()
    {
        return match($this->overall_rating) {
            'excellent'         => 'badge-success',
            'good'              => 'badge-info',
            'satisfactory'      => 'badge-warning',
            'needs_improvement' => 'badge-danger',
            'unsatisfactory'    => 'badge-danger',
            default             => 'badge-secondary',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed'   => 'badge-success',
            'in_progress' => 'badge-info',
            'pending'     => 'badge-warning',
            'cancelled'   => 'badge-secondary',
            default       => 'badge-secondary',
        };
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueSoon($query, $days = 14)
    {
        return $query->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays($days));
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', '<', now());
    }
}