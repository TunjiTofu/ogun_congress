<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgrammeSession extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'is_open'   => 'boolean',
        ];
    }

    public function checkinEvents(): HasMany
    {
        return $this->hasMany(CheckinEvent::class, 'session_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }
}
