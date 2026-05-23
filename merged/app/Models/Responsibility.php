<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsibility extends Model
{
    protected $fillable = ['staff_id', 'description'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}