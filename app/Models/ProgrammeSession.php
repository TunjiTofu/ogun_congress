<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgrammeSession extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date'      => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkinEvents(): HasMany
    {
        return $this->hasMany(CheckinEvent::class, 'programme_session_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(CheckinEvent::class, 'programme_session_id')
            ->where('event_type', 'programme_attendance');
    }

    public function getAttendeeCountAttribute(): int
    {
        return $this->attendees()->distinct('camper_id')->count('camper_id');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }
}
