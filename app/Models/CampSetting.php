<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CampSetting extends Model
{
    protected $table      = 'camp_settings';
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value', 'label', 'group'];

    protected static function booted(): void
    {
        // Inline the cache clearing so this model works even before
        // helpers.php is autoloaded (e.g. during seeding).
        $clearCache = fn (CampSetting $s) =>
        Cache::forget("camp_setting:{$s->key}");

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
