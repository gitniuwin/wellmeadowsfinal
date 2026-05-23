<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_id', 'bed_number', 'status', 'patient_id', 'assigned_at', 'notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function isVacant(): bool
    {
        return $this->status === 'vacant';
    }
}
