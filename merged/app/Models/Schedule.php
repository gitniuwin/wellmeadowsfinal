<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'staff_id', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'
    ];

    protected $casts = [
        'mon' => 'boolean', 'tue' => 'boolean', 'wed' => 'boolean',
        'thu' => 'boolean', 'fri' => 'boolean', 'sat' => 'boolean', 'sun' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}