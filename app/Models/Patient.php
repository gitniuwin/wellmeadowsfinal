<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_number', 'first_name', 'last_name', 'date_of_birth',
        'gender', 'contact_number', 'address', 'is_admitted'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_admitted' => 'boolean',
    ];

    public function bed()
    {
        return $this->hasOne(Bed::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}