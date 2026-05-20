<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'first_name', 'last_name', 'role',
        'department', 'ward', 'shift',
        'email', 'phone', 'status'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(Responsibility::class);
    }
}