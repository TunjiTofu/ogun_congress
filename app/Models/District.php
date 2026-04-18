<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class District extends Model
{
    protected $fillable = ['name', 'zone'];

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    /**
     * Campers belong to a church which belongs to a district.
     * Access via hasManyThrough — never directly via district_id on campers.
     */
    public function campers(): HasManyThrough
    {
        return $this->hasManyThrough(Camper::class, Church::class);
    }
}
