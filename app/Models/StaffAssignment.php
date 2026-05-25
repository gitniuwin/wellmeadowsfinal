<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'patient_id', 'treatment_id', 'assigned_date',
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}