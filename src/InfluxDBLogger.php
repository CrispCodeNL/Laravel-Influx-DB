<?php

namespace CrispCode\LaravelInfluxDB;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InfluxDB2\ApiException;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use InfluxDB2\WriteApi;
use Monolog\DateTimeImmutable;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use UnitEnum;

class InfluxDBLogger extends AbstractHandler
{
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

    public function handle(array $record): bool
    {
        try {
            /** @var WriteApi $api */
            $api = app(WriteApi::class);

            $api->write(
                $this->parseToPoint($record),
                WritePrecision::S, // The underlying library only pretends to be able to handle sub-second accuracy
                $this->bucket,
                $this->organization
            );
            return true;
        } catch (ApiException $ex) {
            // Bubble the message up the handler stack
            return false;
        }
    }

    /**
     * @param array<string, mixed> $record
     * @return Point
     */
    public function parseToPoint(array $record): Point
    {
        /**
         * @var array<string, mixed> $context
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
            $point->addField($key, $this->normalizeValue($value));
        }

        return $point->addField('message', $message)
            ->addTag('level', $level)
            ->time($datetime->format('U'));
    }

    public function normalizeValue(mixed $value): string
    {
        if (is_array($value)) {
            return '[' . implode(', ', array_map([$this, 'normalizeValue'], $value)) . ']';
        }
        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        if (is_callable($value)) {
            throw new Exception("InfluxDBLogger cannot serialize callables");
        }
        if ($value instanceof UnitEnum) {
            return sprintf(
                "%s::%s",
                Str::afterLast(get_class($value), "\\"),
                $value->name,
            );
        }
        if (is_float($value)) {
            return sprintf("%F", $value);
        }
        if (is_int($value)) {
            return sprintf("%d", $value);
        }
        if (is_null($value)) {
            return "NULL";
        }
        if (is_object($value)) {
            if ($value instanceof Model) {
                return sprintf(
                    "%s(%s=%s)",
                    Str::afterLast(get_class($value), "\\"),
                    $value->getKeyName(),
                    $this->normalizeValue($value->getKey()),
                );
            }
            return sprintf(
                "%s@%s",
                Str::afterLast(get_class($value), "\\"),
                spl_object_id($value)
            );
        }
        if (is_resource($value)) {
            return sprintf(
                "%s@%s",
                get_resource_type($value),
                get_resource_id($value)
            );
        }
        if (is_string($value)) {
            return $value;
        }

        throw new Exception("InfluxDBLogger failed to serialize context variable");
    }
}
