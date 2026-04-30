<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CamperHealth extends Model
{
    protected $table = 'camper_health';

    protected $fillable = [
        'camper_id',
        'medical_conditions',
        'medications',
        'allergies',
        'dietary_restrictions',
        'doctor_name',
        'doctor_phone',
        'insurance_details',
        'has_alert',
    ];

    protected function casts(): array
    {
        return [
            'has_alert' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }

    // ── Hooks ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (CamperHealth $health) {
            // Compute has_alert whenever the record is saved
            $health->has_alert = filled($health->medical_conditions)
                || filled($health->medications)
                || filled($health->allergies);
        });
    }
}
