<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'year','day_number','session',
        'appraisee_name','appraisee_staff_id',
        'scheduled_date','start_time','end_time','venue',
        'appraiser_name','observer_names',
        'prepared_by','prepared_date','approved_by','approved_date',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'prepared_date'  => 'date',
        'approved_date'  => 'date',
    ];

    public function appraiseeStaff()
    {
        return $this->belongsTo(StaffProfile::class, 'appraisee_staff_id');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year)->orderBy('scheduled_date')->orderBy('session');
    }

    public static function availableYears(): array
    {
        return static::distinct()->orderBy('year','desc')->pluck('year')->toArray();
    }
}