<?php

namespace CrispCode\LaravelInfluxDB;

use  Illuminate\Support\ServiceProvider;

class InfluxDBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'influxdb');
    }

    public function boot(): void
    {

    }
}
