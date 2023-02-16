<?php
return [
    'udp_port' => env('INFLUXDB_UDP_PORT', 8089),
    'server' => env('INFLUXDB_SERVER'),
    'token' => env('INFLUXDB_TOKEN'),
    'client_opts' => [],
    'organization' => env('INFLUXDB_ORGANIZATION'),
    'bucket' => env('INFLUXDB_BUCKET'),
];
