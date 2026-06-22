<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Channel Drivers
    |--------------------------------------------------------------------------
    | Built-in: 'email'. For 'sms' and 'whatsapp' provide callables that accept
    | signature: function(string $to, string $message, array $context): void {}
    | Example binding in a service provider or route file:
    | app('2fa.sender.sms', fn($to, $msg, $ctx) => YourSmsService::send($to, $msg));
    */
    'channels' => [
        'email' => true,
        'sms' => false,
        'whatsapp' => false,
    ],

    // Code length for self-generated codes
    'code_length' => 6,

    // Expiry (seconds) for self-generated codes
    'self_generated_ttl' => env('TWO_FA_TTL', 300),

    // Throttling / fraud controls
    'max_attempts' => 5,                 // per code
    'lock_minutes_after_max' => 15,      // user lock duration
    'ip_rate_limit' => [                 // soft rate limit
        'max_per_minute' => 10,
        'burst' => 20,
    ],
    'cooldown_seconds_between_requests' => 30,

    // Whether to hash self-generated codes at rest
    'hash_codes' => true,

    // Drift (seconds) allowed for TOTP verification
    'totp_drift' => 30,

    // Issuer for Google Authenticator
    'issuer' => env('APP_NAME', 'Laravel'),

    // Table names (override if needed)
    'tables' => [
        'codes' => 'two_fa_codes',
        'settings' => 'two_fa_settings',
        'logs' => 'two_fa_logs',
    ],
];
