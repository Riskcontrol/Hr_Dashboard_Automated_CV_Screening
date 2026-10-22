<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $attributes = ['status' => 'active'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an HR manager
     *
     * @return bool
     */
    public function isHRManager()
    {
        return $this->role === 'hr_manager';
    }

    /**
     * Check if user has admin privileges (admin or hr_manager)
     *
     * @return bool
     */
    public function hasAdminPrivileges()
    {
        return $this->isAdmin() || $this->isHRManager();
    }

    /**
     * Check if user is a regular staff member (not HR/admin).
     * Staff users only get access to their own Leave and Appraisal records.
     *
     * @return bool
     */
    public function isStaff()
    {
        return !$this->hasAdminPrivileges();
    }

    /**
     * The HR staff record this login account belongs to (if any).
     * Used to scope a staff user's view to their own Leave/Appraisal data.
     */
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class, 'user_id');
    }

    /**
     * Relationship with keyword sets
     */
    public function keywordSets()
    {
        return $this->hasMany(KeywordSet::class, 'created_by');
    }
}