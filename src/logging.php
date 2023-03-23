<?php

return [
    'influxdb' => [
        'driver' => 'monolog',
        'handler' => \CrispCode\LaravelInfluxDB\InfluxDBLogger::class,
        'with' => [
            'organization' => env('INFLUXDB_LOGS_ORGANIZATION') ?? config('influxdb.organization'),
            'bucket' => env('INFLUXDB_LOGS_BUCKET') ?? config('influxdb.bucket'),
            'measurement' => env('INFLUXDB_LOGS_MEASUREMENT', 'logs'),
        ],
    ],
];
