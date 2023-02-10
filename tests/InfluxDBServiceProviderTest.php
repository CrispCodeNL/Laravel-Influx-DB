<?php

namespace CrispCode\LaravelInfluxDB\Tests;

use InfluxDB2\Client;

class InfluxDBServiceProviderTest extends TestCase {
    public function test_it_registers_a_client() {
        $client = $this->app->get(Client::class);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_it_requires_server_to_register() {
        $this->assertThrows(function () {
            $this->app->config->set('influxdb.server', null);
            $client = $this->app->get(Client::class);
            $this->assertInstanceOf(Client::class, $client);
        }, \Exception::class, "INFLUXDB_SERVER");
    }

    public function test_it_requires_token_to_register() {
        $this->assertThrows(function () {
            $this->app->config->set('influxdb.token', null);
            $client = $this->app->get(Client::class);
            $this->assertInstanceOf(Client::class, $client);
        }, \Exception::class, "INFLUXDB_TOKEN");
    }

    public function test_it_respects_client_opts() {
        $this->app->config->set('influxdb.client_opts', ['test_key' => 'test_value']);
        /** @var Client $client */
        $client = $this->app->get(Client::class);
        $this->assertArrayHasKey('test_key', $client->options);
        $this->assertEquals('test_value', $client->options['test_key']);
    }
}
