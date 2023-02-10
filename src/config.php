<?php
return [
    'server' => env('INFLUX_SERVER') ?? throw new Exception("Please set the `INFLUX_SERVER` variable in your environment"),
    'token' => env('INFLUX_TOKEN') ?? throw new Exception("Please set the `INFLUX_TOKEN` variable in your environment"),
];
