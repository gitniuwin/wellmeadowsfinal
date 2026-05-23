<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'capacity', 'floor', 'building', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function occupiedBeds()
    {
        return $this->hasMany(Bed::class)->where('status', 'occupied');
    }

    public function vacantBeds()
    {
        return $this->hasMany(Bed::class)->where('status', 'vacant');
    }

    public function getOccupancyPercentageAttribute(): int
    {
        if ($this->capacity === 0) return 0;
        return (int) round(($this->beds->where('status', 'occupied')->count() / $this->capacity) * 100);
    }

    public function getAvailabilityStatusAttribute(): string
    {
        $pct = $this->occupancy_percentage;
        if ($pct >= 100) return 'full';
        if ($pct >= 80) return 'limited';
        return 'available';
    }
}
