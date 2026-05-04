<?php

namespace App\Models;

use App\Enums\CheckinEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinEvent extends Model
{
    protected $fillable = [
        'uuid',
        'camper_id',
        'event_type',
        'programme_session_id',
        'occurred_at',
        'device_id',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'event_type'  => CheckinEventType::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
