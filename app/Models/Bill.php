<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_name',
        'service_type',
        'total_amount',
        'due_date',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Total money already paid across all payments for this bill
    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    // How much is still owed
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->amount_paid);
    }

    public function patient()
    {
    return $this->belongsTo(\App\Models\Patient::class);
    }
}