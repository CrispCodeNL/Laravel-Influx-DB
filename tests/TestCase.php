<?php

namespace CrispCode\LaravelInfluxDB\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            \CrispCode\LaravelInfluxDB\InfluxDBServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->config->set('influxdb.server', 'http://localhost:8086');
        $app->config->set('influxdb.token', 'TESTING-TOKEN');
    }
}
