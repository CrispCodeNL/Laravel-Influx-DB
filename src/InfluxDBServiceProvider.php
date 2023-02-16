<?php

namespace CrispCode\LaravelInfluxDB;

use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InfluxDB2\Client;
use InfluxDB2\InvokableScriptsApi;
use InfluxDB2\QueryApi;
use InfluxDB2\UdpWriter;
use InfluxDB2\WriteApi;

class InfluxDBServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'influxdb');
        $this->mergeConfigFrom(__DIR__ . '/logging.php', 'logging.channels');

        $this->app->singleton(Client::class, fn(Application $app) => new Client([
            'url' => config('influxdb.server') ?? throw new Exception('Please set the `INFLUXDB_SERVER` variable in your environment'),
            'token' => config('influxdb.token') ?? throw new Exception('Please set the `INFLUXDB_TOKEN` variable in your environment'),
            'udpPort' => config('influxdb.udp_port'),
            ...array(config('influxdb.client_opts', [])),
        ]));

        $this->app->singleton(InvokableScriptsApi::class, function (Application $app) {
            /** @var Client $client */
            $client = $app->get(Client::class);
            return $client->createInvokableScriptsApi();
        });
        $this->app->singleton(QueryApi::class, function (Application $app) {
            /** @var Client $client */
            $client = $app->get(Client::class);
            return $client->createQueryApi();
        });
        $this->app->singleton(UdpWriter::class, function (Application $app) {
            /** @var Client $client */
            $client = $app->get(Client::class);
            return $client->createUdpWriter();
        });
        $this->app->singleton(WriteApi::class, function (Application $app) {
            /** @var Client $client */
            $client = $app->get(Client::class);
            return $client->createWriteApi();
        });
    }

    public function boot(): void {

    }
}
