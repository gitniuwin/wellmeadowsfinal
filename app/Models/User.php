<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email',
        'password', 'role', 'department', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // Role checkers
    public function isMedicalDirector(): bool
    {
        return $this->role === 'Medical Director';
    }

    public function isChargeNurse(): bool
    {
        return $this->role === 'Charge Nurse';
    }

    public function isHRStaff(): bool
    {
        return $this->role === 'Personnel/HR Staff';
    }

    // Full access roles
    public function hasFullAccess(): bool
    {
        return in_array($this->role, ['Medical Director', 'Personnel/HR Staff']);
    }

    // Can edit staff records
    public function canManageStaff(): bool
    {
        return in_array($this->role, ['Medical Director', 'Personnel/HR Staff']);
    }

    // Can view schedules only (Charge Nurse)
    public function canViewSchedules(): bool
    {
        return true; // all roles can view
    }

    // Can edit schedules
    public function canEditSchedules(): bool
    {
        return in_array($this->role, ['Medical Director', 'Personnel/HR Staff']);
    }
}