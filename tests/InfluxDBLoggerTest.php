<?php

namespace CrispCode\LaravelInfluxDB\Tests;

use CrispCode\LaravelInfluxDB\InfluxDBLogger;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InfluxDB2\WriteApi;
use Monolog\DateTimeImmutable;
use Monolog\Handler\StreamHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class InfluxDBLoggerTest extends TestCase {
    private LoggerInterface $channel;

    public function setUp(): void {
        parent::setUp();

        $this->app->config->set('logging.channels.influxdb.with.organization', 'crispcode');
        $this->app->config->set('logging.channels.influxdb.with.bucket', 'default');

        $this->channel = Log::channel('influxdb');
    }

    public function test_something() {
        $intercepted = false;
        /** @var WriteApi $api */
        $api = app(WriteApi::class);
        $spy = new Spy($api, 'http');
        $spy->intercept('sendRequest', function (Request $request) use (&$intercepted) {
            $intercepted = true;
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('http', $request->getUri()->getScheme());
            $this->assertEquals('localhost', $request->getUri()->getHost());
            $this->assertEquals('8086', $request->getUri()->getPort());
            $this->assertEquals('/api/v2/write', $request->getUri()->getPath());
            $this->assertEquals('org=crispcode&bucket=default&precision=s', $request->getUri()->getQuery());
            $this->assertEquals('logs,level=EMERGENCY custom_ctx="present",message="Something went right" ' . date('U'), $request->getBody()->getContents());
            return new Response();
        });
        $this->channel->emergency("Something went right", ['custom_ctx' => 'present']);
        $this->assertTrue($intercepted, "Interceptor should've been called");
    }

    public function test_throws_when_organization_absent() {
        $this->app->config->set('logging.channels.influxdb.with.organization', null);
        $this->app->config->set('logging.channels.influxdb.with.bucket', 'default');
        Log::forgetChannel('influxdb');

        $logger = Log::channel('influxdb');
        $this->assertInstanceOf(Logger::class, $logger);
        $handler = $logger->getHandlers()[0];
        $this->assertInstanceOf(StreamHandler::class, $handler);
        $uri = stream_get_meta_data($handler->getStream())['uri'];
        $this->assertFileExists($uri);

        $content = file_get_contents($uri);
        $this->assertStringContainsString('Unable to create configured logger', $content);
        $this->assertStringContainsString('INFLUXDB_LOGS_ORGANIZATION', $content);

        unlink($uri);
        $this->assertFileDoesNotExist($uri);
    }

    public function test_throws_when_bucket_absent() {
        $this->app->config->set('logging.channels.influxdb.with.organization', 'crispcode');
        $this->app->config->set('logging.channels.influxdb.with.bucket', null);
        Log::forgetChannel('influxdb');

        $logger = Log::channel('influxdb');
        $this->assertInstanceOf(Logger::class, $logger);
        $handler = $logger->getHandlers()[0];
        $this->assertInstanceOf(StreamHandler::class, $handler);
        $uri = stream_get_meta_data($handler->getStream())['uri'];
        $this->assertFileExists($uri);

        $content = file_get_contents($uri);
        $this->assertStringContainsString('Unable to create configured logger', $content);
        $this->assertStringContainsString('INFLUXDB_LOGS_BUCKET', $content);

        unlink($uri);
        $this->assertFileDoesNotExist($uri);
    }

    public function test_parse_record_to_point() {
        /** @var Logger $logger */
        $logger = (new ReflectionClass($this->channel))->getProperty('logger')->getValue($this->channel);
        /** @var InfluxDBLogger $handler */
        [$handler] = (new ReflectionClass($logger))->getProperty('handlers')->getValue($logger);

        $this->assertInstanceOf(InfluxDBLogger::class, $handler);

        $point = $handler->parseToPoint([
            'context' => ['ctx' => 'phpunit testing'],
            'datetime' => new DateTimeImmutable(false),
            'level_name' => 'EMERGENCY',
            'message' => 'TEST MESSAGE!',
        ]);

        $this->assertEquals('logs,level=EMERGENCY ctx="phpunit testing",message="TEST MESSAGE!" ' . date('U'), $point->toLineProtocol());
    }
}
