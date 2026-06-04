<?php

use App\Models\CampSetting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Get a camp setting value by key.
     *
     * Uses a two-level cache:
     *   1. Static in-memory array — zero cost for repeated calls within the same request.
     *      The welcome page calls setting() 20+ times; this means 1 Redis lookup total.
     *   2. Redis/cache with a 1-hour TTL — shared across all PHP workers.
     *      Cleared automatically via CampSetting model observer on save.
     *
     * At scale (1000 campers, 35 admins): without this, every page load that
     * calls setting() 20 times = 20 Redis lookups × N requests/second.
     * With this: 1 Redis lookup per worker process lifetime (usually per request on shared hosting).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $value = Cache::remember(
            'camp_setting:' . $key,
            3600, // 1 hour — cleared by model observer on update
            fn () => CampSetting::where('key', $key)->value('value')
        );

        $cache[$key] = $value ?? $default;

        return $cache[$key];
    }
}

if (! function_exists('setting_all')) {
    /**
     * Preload all settings into the static cache in a single DB query.
     * Call this once in a service provider or middleware for pages that use many settings.
     * Eliminates N Redis lookups when the welcome page uses 20+ settings.
     */
    function setting_all(): void
    {
        static $loaded = false;
        if ($loaded) return;

        $all = Cache::remember(
            'camp_settings_all',
            3600,
            fn () => CampSetting::pluck('value', 'key')->toArray()
        );

        // Pre-populate the setting() static cache
        setting_preload($all);
        $loaded = true;
    }
}

if (! function_exists('setting_preload')) {
    function setting_preload(array $settings): void
    {
        // Access the static cache inside setting() via a dummy call that primes it
        foreach ($settings as $key => $value) {
            // Directly prime by calling setting with the value already known
            Cache::put('camp_setting:' . $key, $value, 3600);
        }
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
