<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_name',
        'applicant_email',
        'phone',
        'cv_original_name',
        'cv_stored_path',
        'cv_file_size',
        'extracted_text',
        'keyword_set_id',
        'qualification_status',
        'match_percentage',
        'found_keywords',
        'missing_keywords',
        'processed_at',
        'processing_status',
        'processing_started_at',
    ];

    protected $casts = [
        'found_keywords' => 'array',
        'missing_keywords' => 'array',
        'match_percentage' => 'decimal:2',
        'processed_at' => 'datetime',
        'processing_started_at' => 'datetime',
    ];

    public function keywordSet()
    {
        return $this->belongsTo(KeywordSet::class);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->cv_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isQualified()
    {
        return $this->qualification_status === 'qualified';
    }

    public function isFairlyQualified()
    {
        return $this->qualification_status === 'Fairly Qualified';
    }

    public function isPending()
    {
        return $this->qualification_status === 'pending';
    }

    public function scopeQualified($query)
    {
        return $query->where('qualification_status', 'qualified');
    }

    public function scopeNotQualified($query)
    {
        return $query->where('qualification_status', 'not_qualified');
    }

    public function scopeFairlyQualified($query)
    {
        return $query->where('qualification_status', 'Fairly Qualified');
    }

    public function scopePending($query)
    {
        return $query->where('qualification_status', 'pending');
    }
}
