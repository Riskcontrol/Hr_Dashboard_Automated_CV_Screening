<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'action',
        'status',
        'error_message',
        'processing_time_ms',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getProcessingTimeFormattedAttribute()
    {
        if (!$this->processing_time_ms) return 'N/A';
        
        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . 'ms';
        }
        
        return round($this->processing_time_ms / 1000, 2) . 's';
    }
}