<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        // EN RED Y LA NUVE
        'https://apidesarrollos.virreysolisips.com.co:1801/api/*',
        // LOCALES

        "http://localhost:8000/api/*",
        "http://10.10.6.220:8000/api/*",
        "http://10.10.6.228:8000/api/*",
        "http://10.10.6.242:8000/api/*",
        "http://10.10.6.72:8000/api/*",
        "http://10.10.6.74:8000/api/*",
    ],

    'allowed_methods' => [
        'GET',
        'POST'
    ],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
