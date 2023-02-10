<?php

namespace CrispCode\LaravelInfluxDB;

use Exception;
use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client;

class InfluxDBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'influxdb');

        $this->app->singleton(Client::class, fn($app) => new Client([
            'url' => config('influxdb.server') ?? throw new Exception('Please set the `INFLUXDB_SERVER` variable in your environment'),
            'token' => config('influxdb.token') ?? throw new Exception('Please set the `INFLUXDB_TOKEN` variable in your environment'),
            ...config('influxdb.client_opts', []),
        ]));

        /** @var Client $client */
//        $client=$this->app->get(Client::class);
//        $client->createInvokableScriptsApi();
//        $client->createUdpWriter();
//        $client->createWriteApi();
//        $client->createQueryApi();
    }

    public function boot(): void
    {

    }
}
