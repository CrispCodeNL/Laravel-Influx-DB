<?php

namespace Crispcode\LaravelInfluxDB;

use Illuminate\Support\ServiceProvider;
use Crispcode\LaravelInfluxDB\Console\InfluxPersister;

class InfluxServiceProvider extends ServiceProvider
{
    public const LOG_LOCATION = 'logs/influx.log';

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'influx-db');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('influx.php'),
            ], 'config');

            $this->commands([
                InfluxPersister::class
            ]);
        }
    }
}