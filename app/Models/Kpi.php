<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Kpi Model
 * Represents a Key Performance Indicator assigned to a staff member.
 * Score and rating are auto-calculated from target vs actual values.
 */
class Kpi extends Model
{
    use HasFactory;

    protected $table = 'kpis';

    protected $fillable = [
        'staff_profile_id',
        'title', 'description', 'category', 'department',
        'period_type', 'period_start', 'period_end',
        'target_value', 'actual_value', 'unit',
        'score_percentage', 'rating',
        'status', 'notes', 'reviewed_by',
    ];

    protected $casts = [
        'period_start'     => 'date',
        'period_end'       => 'date',
        'target_value'     => 'decimal:2',
        'actual_value'     => 'decimal:2',
        'score_percentage' => 'decimal:2',
    ];

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    public function getRatingColorAttribute()
    {
        return match($this->rating) {
            'excellent' => 'badge-success',
            'good'      => 'badge-info',
            'average'   => 'badge-warning',
            'poor', 'critical' => 'badge-danger',
            default     => 'badge-secondary',
        };
    }

    public function getProgressColorAttribute()
    {
        $score = $this->score_percentage ?? 0;
        if ($score >= 90) return 'bg-green-500';
        if ($score >= 70) return 'bg-blue-500';
        if ($score >= 50) return 'bg-yellow-500';
        return 'bg-red-500';
    }

    public function getPeriodTypeLabelAttribute()
    {
        return match($this->period_type) {
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually'  => 'Annual',
            default     => ucfirst($this->period_type),
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function calculateScore($target, $actual): float
    {
        if (!$target || $target == 0) return 0;
        return min(round(($actual / $target) * 100, 2), 100);
    }

    public static function determineRating(float $score): string
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 70) return 'good';
        if ($score >= 50) return 'average';
        if ($score >= 30) return 'poor';
        return 'critical';
    }
}