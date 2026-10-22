<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'keywords',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];

    // Ensure keywords are always returned as an array
    public function getKeywordsAttribute($value)
    {
        if (is_string($value)) {
            // Handle double-encoded JSON (common issue)
            $decoded = json_decode($value, true);
            
            // If first decode gives us a string, decode again
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Ensure keywords are always stored as JSON
    public function setKeywordsAttribute($value)
    {
        $this->attributes['keywords'] = is_array($value) ? json_encode($value) : $value;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}