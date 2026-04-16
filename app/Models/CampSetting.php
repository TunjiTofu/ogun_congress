<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampSetting extends Model
{
    protected $table      = 'camp_settings';
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value', 'label', 'group'];

    protected static function booted(): void
    {
        // Clear the Redis cache for this key whenever the value is updated
        static::saved(fn (CampSetting $s) => clear_setting_cache($s->key));
        static::deleted(fn (CampSetting $s) => clear_setting_cache($s->key));
    }
}
