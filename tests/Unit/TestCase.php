<?php

namespace Unit;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            Crispode\LaravelInfluxDB\InfluxDBServiceProivder::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
    }
}