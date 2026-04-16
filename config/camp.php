<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Camp-specific configuration
    |--------------------------------------------------------------------------
    |
    | Static defaults. Runtime values are stored in the camp_settings table
    | and retrieved via the setting() helper, which checks the DB first
    | and falls back to these values.
    |
    */

    'code_expiry_days' => (int) env('CAMP_CODE_EXPIRY_DAYS', 14),

    'registration_fees' => [
        'adventurer'   => (int) env('CAMP_REGISTRATION_FEE_ADVENTURER', 5000),
        'pathfinder'   => (int) env('CAMP_REGISTRATION_FEE_PATHFINDER', 5000),
        'senior_youth' => (int) env('CAMP_REGISTRATION_FEE_SENIOR_YOUTH', 7000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limiting (requests per window)
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'payment_initiate' => ['attempts' => 5,   'decay_minutes' => 10],
        'code_validate'    => ['attempts' => 10,  'decay_minutes' => 15],
        'checkin_api'      => ['attempts' => 300, 'decay_minutes' => 1],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document generation
    |--------------------------------------------------------------------------
    */

    'documents' => [
        'id_card_disk'       => 'private',
        'id_card_path'       => 'id-cards',
        'consent_form_disk'  => 'private',
        'consent_form_path'  => 'consent-forms',
        'url_expiry_hours'   => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default badge colours per category
    |--------------------------------------------------------------------------
    */

    'badge_colors' => [
        'adventurer'   => '#1B6BB5',
        'pathfinder'   => '#1A6B3A',
        'senior_youth' => '#C9A94D',
    ],

];
