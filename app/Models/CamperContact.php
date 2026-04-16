<?php

namespace App\Models;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CamperContact extends Model
{
    protected $fillable = [
        'camper_id',
        'type',
        'full_name',
        'relationship',
        'phone',
        'email',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'type'       => ContactType::class,
            'is_primary' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeParents($query)
    {
        return $query->where('type', ContactType::PARENT_GUARDIAN);
    }

    public function scopeEmergency($query)
    {
        return $query->where('type', ContactType::EMERGENCY_CONTACT);
    }
}
