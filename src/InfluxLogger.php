<?php

namespace Crispcode\LaravelInfluxDB;

use Crispcode\LaravelInfluxDB\Console\InfluxPersister;
use Monolog\Handler\HandlerInterface;
class InfluxLogger implements HandlerInterface
{
    private $file;
    private bool $hasError;
    private array $context;
    private string $format;

    public function __construct()
    {
        $this->format = config('influx.format');
        $this->context = config('influx.context');

        $file = storage_path(InfluxServiceProvider::LOG_LOCATION);
        if (!file_exists($file)) {
            touch($file);
        }

        $this->file = fopen($file, 'a');
        register_shutdown_function($this, 'close');
    }

    /**
     * @inheritDoc
     */
    public function isHandling(array $record): bool
    {
        return  true;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $record): bool
    {
        $this->hasError |= $record['level_name'] === 'ERROR';
        $message = $this->formatString($this->format, $record);
        $tags = array_merge($record['context'], $this->context);

        foreach ($tags as $tag => $value) {
            if (is_string($value)) {
                $tags[$tag] = $this->formatString($value, $record);
            } else {
                unset($tags[$tag]);
            }
        }

        return fwrite($this->file, json_encode([
            'time' => now()->getPreciseTimestamp(),
            'tags' => $tags,
            'message' => $message
        ]) . "\n");
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->hasError) {
            $persister = new InfluxPersister();
            $persister->handle();
        }
    }

    private function formatString(string $format, array $context): string
    {
        $message = $format;

        foreach ($context as $key => $value) {
            if (!is_string($value)) continue;
            $message = str_replace(
                sprintf('{%s}', $key),
                $value,
                $message
            );
        }

        return $message;
    }
}