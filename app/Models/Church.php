<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Church extends Model
{
    protected $fillable = ['district_id', 'name', 'address'];

    // ── Relationships ─────────────────────────────────────────────────────────

    /**
     * A church belongs to exactly one district.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function campers(): HasMany
    {
        return $this->hasMany(Camper::class);
    }
}
