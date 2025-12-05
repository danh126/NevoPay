<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for One-Time Password (OTP) functionality,
    |
    */

    'ttl' => env('OTP_TTL'),
    'secret' => env('OTP_SECRET'),
    'max_attempts' => env('OTP_MAX_ATTEMPTS'),

];