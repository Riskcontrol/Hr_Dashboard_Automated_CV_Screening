<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PensionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_profile_id',
        'pension_provider',
        'pension_pin',
        'rsa_number',
        'employee_contribution',
        'employer_contribution',
        'contribution_type',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'enrollment_date'       => 'date',
        'employee_contribution' => 'decimal:2',
        'employer_contribution' => 'decimal:2',
    ];

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    public function getContributionSummaryAttribute(): string
    {
        $suffix = $this->contribution_type === 'percentage' ? '%' : ' (fixed)';
        return "Employee: {$this->employee_contribution}{$suffix} / Employer: {$this->employer_contribution}{$suffix}";
    }
}