<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $guarded = [];

    public static function record(string $action, string $module, string $description): self
    {
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Guest',
            'user_role' => $user?->role ?? 'guest',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function getActionColorAttribute(): string
    {
        return $this->action === 'login' ? 'badge-success' : 'badge-secondary';
    }

    public function getActionIconAttribute(): string
    {
        return $this->action === 'login' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
    }
}
