<?php

namespace CrispCode\LaravelInfluxDB;

use Exception;
use Illuminate\Support\Facades\Log;
use InfluxDB2\ApiException;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use InfluxDB2\WriteApi;
use Monolog\DateTimeImmutable;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;

class InfluxDBLogger extends AbstractHandler {
    public readonly string $organization;
    public readonly string $bucket;

    public function __construct(
        ?string                 $organization,
        ?string                 $bucket,
        private readonly string $measurement = 'laravel',
                                $level = Logger::DEBUG,
        bool                    $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->organization = $organization ?? throw new Exception("Please set the `INFLUXDB_LOGS_ORGANIZATION` or `INFLUXDB_ORGANIZATION variable in your environment");
        $this->bucket = $bucket ?? throw new Exception('Please set the `INFLUXDB_LOGS_BUCKET` or `INFLUXDB_BUCKET variable in your environment');
    }

    public function handle(array $record): bool {
        try {
            /** @var WriteApi $api */
            $api = app(WriteApi::class);

            $api->write(
                $this->parseToPoint($record),
                WritePrecision::S, // The underlying library only pretends to be able to handle sub-second accuracy
                $this->bucket,
                $this->organization
            );
        } catch (ApiException $ex) {
            Log::channel('emergency')->emergency($record);
        }
        return true;
    }

    public function parseToPoint(array $record): Point {
        /**
         * @var array $context
         * @var DateTimeImmutable $datetime
         * @var string $level
         * @var string $message
         */
        [
            'context' => $context,
            'datetime' => $datetime,
            'level_name' => $level,
            'message' => $message,
        ] = $record;

        $point = Point::measurement($this->measurement);

        foreach ($context as $key => $value) {
            $point->addField($key, $value);
        }

        return $point->addField('message', $message)
            ->addTag('level', $level)
            ->time($datetime->format('U'));

    }
}
