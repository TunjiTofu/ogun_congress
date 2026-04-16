<?php

namespace App\Models;

use App\Enums\CheckinEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'camper_id',
        'event_type',
        'session_id',
        'scanned_by',
        'device_id',
        'scanned_at',
        'synced_at',
        'consent_collected',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_type'        => CheckinEventType::class,
            'scanned_at'        => 'datetime',
            'synced_at'         => 'datetime',
            'consent_collected' => 'boolean',
        ];
    }

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ProgrammeSession::class, 'session_id');
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
