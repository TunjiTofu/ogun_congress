<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'zone'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    public function campers(): HasMany
    {
        return $this->hasMany(Camper::class);
    }
}
