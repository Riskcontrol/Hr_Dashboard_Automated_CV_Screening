<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

/**
 * StaffProfile Model
 * 
 * Represents a full employee record in the HR system.
 * Each staff member has personal info, employment details,
 * compensation, and can be linked to a CV application they submitted.
 */
// class StaffProfile extends Model
// {
//     use HasFactory;

    /**
     * All fields that can be saved via forms.
     * Laravel requires this for security (mass assignment protection).
     */
    // protected $fillable = [
        // Personal Information
        // 'full_name', 'employee_id', 'gender', 'date_of_birth', 'marital_status',
        // 'nationality', 'profile_photo',

        // Contact Details
        // 'phone_number', 'email', 'residential_address',
        // 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',

        // Employment Details
        // 'job_title', 'department', 'location', 'employment_type', 'date_of_hire', 'status',
        // 'line_manager', 'department_head',

        // Identification & Compliance
        // 'national_id', 'tax_id', 'pension_details',

        // Compensation
        // 'salary', 'bank_name', 'bank_account_number',

        // Education & Work History
        // 'academic_background', 'certifications', 'professional_memberships',
        // 'previous_roles', 'promotion_history',

        // Link to original CV application (if hired through the system)
    //     'application_id',
    // ];

    /**
     * Cast these fields to proper data types automatically.
     * Dates become Carbon objects so we can call ->format(), ->age, etc.
     */
    // protected $casts = [
    //     'date_of_birth' => 'date',
    //     'date_of_hire'  => 'date',
    //     'salary'        => 'decimal:2',
    // ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * A staff profile may have been created from a CV application.
     * This links back to that original application record.
     */
    // public function application()
    // {
    //     return $this->belongsTo(Application::class);
    // }

    // =========================================================
    // COMPUTED ATTRIBUTES (accessed like $staff->age)
    // =========================================================

    /**
     * Calculate the employee's age from their date of birth.
     * Returns null if date of birth is not set.
     */
    // public function getAgeAttribute()
    // {
    //     return $this->date_of_birth ? $this->date_of_birth->age : null;
    // }

    /**
     * Generate initials from the employee's full name.
     * e.g. "Naomi Nosa" → "NN", "John Doe Smith" → "JD"
     */
    // public function getInitialsAttribute()
    // {
    //     $parts    = explode(' ', $this->full_name);
    //     $initials = '';
    //     foreach (array_slice($parts, 0, 2) as $part) {
    //         $initials .= strtoupper(substr($part, 0, 1));
    //     }
    //     return $initials;
    // }

    /**
     * Return a human-readable label for the employment type.
     * Converts database values like "full_time" → "Full Time"
     */
    // public function getEmploymentTypeLabelAttribute()
    // {
    //     return match($this->employment_type) {
    //         'full_time' => 'Full Time',
    //         'part_time' => 'Part Time',
    //         'contract'  => 'Contract',
    //         'intern'    => 'Intern',
    //         default     => ucfirst($this->employment_type ?? ''),
    //     };
    // }

    // =========================================================
    // QUERY SCOPES (reusable filters for database queries)
    // =========================================================

    /**
     * Scope: only return active employees.
     * Usage: StaffProfile::active()->get()
     */
    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }

    /**
     * Scope: filter by department.
     * Usage: StaffProfile::byDepartment('Finance')->get()
     */
    // public function scopeByDepartment($query, $dept)
    // {
    //     return $query->where('department', $dept);
    // }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Auto-generate the next employee ID in sequence.
     * Format: EMP0001, EMP0002, EMP0003 ...
     * Called when creating a new staff profile form.
     */
//     public static function generateEmployeeId()
//     {
//         $last = self::orderBy('id', 'desc')->first();
//         $next = $last ? ((int) filter_var($last->employee_id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
//         return 'EMP' . str_pad($next, 4, '0', STR_PAD_LEFT);
//     }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StaffProfile Model
 * 
 * Represents a full employee record in the HR system.
 * Each staff member has personal info, employment details,
 * compensation, and can be linked to a CV application they submitted.
 */
class StaffProfile extends Model
{
    use HasFactory;

    /**
     * All fields that can be saved via forms.
     * Laravel requires this for security (mass assignment protection).
     */
    protected $fillable = [
        // Personal Information
        'full_name', 'employee_id', 'gender', 'date_of_birth', 'marital_status',
        'nationality', 'profile_photo',

        // Contact Details
        'phone_number', 'email', 'residential_address',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',

        // Employment Details
        'job_title', 'department', 'location', 'employment_type', 'date_of_hire', 'status',
        'line_manager', 'department_head',

        // Identification & Compliance
        'national_id', 'tax_id', 'pension_details',

        // Compensation
        'salary', 'bank_name', 'bank_account_number',

        // Education & Work History
        'academic_background', 'certifications', 'professional_memberships',
        'previous_roles', 'promotion_history',

        // Link to original CV application (if hired through the system)
        'application_id',

        // Link to the login account this staff member uses (if they have one)
        'user_id',
    ];

    /**
     * Cast these fields to proper data types automatically.
     * Dates become Carbon objects so we can call ->format(), ->age, etc.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_hire'  => 'date',
        'salary'        => 'decimal:2',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * The login account this staff member uses (if one has been linked).
     * Used so the staff member can log in and see their own Leave and
     * Appraisal records only.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pensionRecord()
{
    return $this->hasOne(PensionRecord::class);
}

    /** All leave requests submitted for this staff member. */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /** All appraisals/probation reviews for this staff member. */
    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    /**
     * A staff profile may have been created from a CV application.
     * This links back to that original application record.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    // =========================================================
    // COMPUTED ATTRIBUTES (accessed like $staff->age)
    // =========================================================

    /**
     * Calculate the employee's age from their date of birth.
     * Returns null if date of birth is not set.
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Generate initials from the employee's full name.
     * e.g. "Naomi Nosa" → "NN", "John Doe Smith" → "JD"
     */
    public function getInitialsAttribute()
    {
        $parts    = explode(' ', $this->full_name);
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        return $initials;
    }

    /**
     * Return a human-readable label for the employment type.
     * Converts database values like "full_time" → "Full Time"
     */
    public function getEmploymentTypeLabelAttribute()
    {
        return match($this->employment_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract'  => 'Contract',
            'intern'    => 'Intern',
            default     => ucfirst($this->employment_type ?? ''),
        };
    }

    // =========================================================
    // QUERY SCOPES (reusable filters for database queries)
    // =========================================================

    /**
     * Scope: only return active employees.
     * Usage: StaffProfile::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: filter by department.
     * Usage: StaffProfile::byDepartment('Finance')->get()
     */
    public function scopeByDepartment($query, $dept)
    {
        return $query->where('department', $dept);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Auto-generate the next employee ID in sequence.
     * Format: EMP0001, EMP0002, EMP0003 ...
     * Called when creating a new staff profile form.
     */
    public static function generateEmployeeId()
    {
        $last = self::orderBy('id', 'desc')->first();
        $next = $last ? ((int) filter_var($last->employee_id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return 'EMP' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}