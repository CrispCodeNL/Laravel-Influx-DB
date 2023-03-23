<?php

namespace CrispCode\LaravelInfluxDB\Tests;

use InfluxDB2\Client;
use InfluxDB2\InvokableScriptsApi;
use InfluxDB2\QueryApi;
use InfluxDB2\UdpWriter;
use InfluxDB2\WriteApi;

class InfluxDBServiceProviderTest extends TestCase
{
    public function registered_services()
    {
        return [
            [Client::class],
            [InvokableScriptsApi::class],
            [QueryApi::class],
            [UdpWriter::class],
            [WriteApi::class],
        ];
    }

    /**
     * @dataProvider registered_services
     */
    public function test_it_registers_services(string $type)
    {
        $this->assertInstanceOf($type, $this->app->get($type));
    }

    public function test_it_requires_server_to_register()
    {
        $this->assertThrows(function () {
            $this->app->config->set('influxdb.server', null);
            $client = $this->app->get(Client::class);
            $this->assertInstanceOf(Client::class, $client);
        }, \Exception::class, "INFLUXDB_SERVER");
    }

    public function test_it_requires_token_to_register()
    {
        $this->assertThrows(function () {
            $this->app->config->set('influxdb.token', null);
            $client = $this->app->get(Client::class);
            $this->assertInstanceOf(Client::class, $client);
        }, \Exception::class, "INFLUXDB_TOKEN");
    }

    public function test_it_respects_client_opts()
    {
        $this->app->config->set('influxdb.client_opts', ['test_key' => 'test_value']);
        /** @var Client $client */
        $client = $this->app->get(Client::class);
        $this->assertArrayHasKey('test_key', $client->options);
        $this->assertEquals('test_value', $client->options['test_key']);
    }
}
