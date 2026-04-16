<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── Paystack ──────────────────────────────────────────────────────────────
    'paystack' => [
        'public_key'       => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key'       => env('PAYSTACK_SECRET_KEY'),
        'payment_url'      => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
        'webhook_secret'   => env('PAYSTACK_WEBHOOK_SECRET'),
        'merchant_email'   => env('PAYSTACK_MERCHANT_EMAIL', 'camp@ogunconference.org'),
    ],

    // ── SMS (shared config, provider-agnostic) ────────────────────────────────
    'sms' => [
        'provider'   => env('SMS_PROVIDER', 'termii'),
        'api_key'    => env('SMS_API_KEY'),
        'sender_id'  => env('SMS_SENDER_ID', 'OgunConf'),
    ],

    // ── Termii ────────────────────────────────────────────────────────────────
    'termii' => [
        'base_url' => env('TERMII_BASE_URL', 'https://api.ng.termii.com/api'),
    ],

    // ── Africa's Talking ──────────────────────────────────────────────────────
    'africas_talking' => [
        'username' => env('AT_USERNAME'),
        'api_key'  => env('AT_API_KEY'),
    ],

];
