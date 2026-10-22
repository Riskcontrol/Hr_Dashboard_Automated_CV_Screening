<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'file_path',
        'file_name',
        'uploaded_by',
        'notify_staff',
        'notified_at',
    ];

    protected $casts = [
        'notify_staff' => 'boolean',
        'notified_at'  => 'datetime',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'handbook'        => 'Employee Handbook',
            'code_of_conduct' => 'Code of Conduct',
            default           => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}