<?php

use App\Models\CampSetting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Retrieve a camp setting by key.
     *
     * Values are cached in Redis for 1 hour.
     * The cache is cleared automatically via CampSetting model observer
     * whenever a setting is updated in the Filament admin panel.
     *
     * @param  string  $key      The setting key (e.g. 'camp_name', 'bank_account_number')
     * @param  mixed   $default  Fallback value if the key does not exist
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            "camp_setting:{$key}",
            now()->addHour(),
            fn () => CampSetting::where('key', $key)->value('value') ?? $default,
        );
    }
}

if (! function_exists('clear_setting_cache')) {
    /**
     * Clear the cache for a specific setting key.
     * Called by CampSetting observer on update.
     */
    function clear_setting_cache(string $key): void
    {
        Cache::forget("camp_setting:{$key}");
    }
}
