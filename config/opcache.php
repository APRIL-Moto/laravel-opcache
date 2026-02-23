<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable HTTP routes
    |--------------------------------------------------------------------------
    |
    | Set to false to disable the HTTP endpoints entirely (opcache-api/*).
    | The Artisan commands (opcache:clear, etc.) will no longer work when
    | disabled, since they rely on the HTTP endpoint to reach the FPM process.
    |
    | You can also set the OPCACHE_ENABLED env variable to false in production
    | if you manage OPcache via another mechanism (e.g. cachetool).
    |
    */

    'enabled' => env('OPCACHE_ENABLED', true),

    'url' => env('OPCACHE_URL', config('app.url')),

    'prefix' => 'opcache-api',

    'verify' => true,

    'headers' => [],

    'directories' => [
        base_path('app'),
        base_path('bootstrap'),
        base_path('public'),
        base_path('resources'),
        base_path('routes'),
        base_path('storage'),
        base_path('vendor'),
    ],

    'exclude' => [
        'test',
        'Test',
        'tests',
        'Tests',
        'stub',
        'Stub',
        'stubs',
        'Stubs',
        'dumper',
        'Dumper',
        'Autoload',
    ],

];
