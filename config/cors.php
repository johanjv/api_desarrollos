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
        /* "https://certificadoescolar.virreysolisips.com.co:1463/api/*",
        "https://citologias.virreysolisips.com.co:1462/api/*",
        "https://factucontrol.virreysolisips.com.co:1461/api/*",
        "https://hvsedes.virreysolisips.com.co:1460/api/*", */
        'https://apidesarrollos.virreysolisips.com.co:1801/api/*',

        /* LOCALES */

        "http://localhost:8000/api/*",
        "http://10.10.6.220:8000/api/*",
        "http://10.10.6.228:8000/api/*",

        /* 'api/*' */
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

    'supports_credentials' => false,

];
