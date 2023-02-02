<?php

return [
    'context' => [
        'bucket' => env('LOG_APP', env('APP_NAME')),
        'type' => '{level_name}'
    ],
    'format' => env('LOG_FORMAT', '[{level_name}] {message}'),
    'host' => [
        'url' => env('LOG_SERVER'),
        'token' => env('LOG_ACCESS_TOKEN'),
        'organisation' => env('LOG_ORGANISATION'),
    ]
];