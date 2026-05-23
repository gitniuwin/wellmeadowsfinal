<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'bill_id',
        'amount',
        'method',
        'processed_by',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
