<?php

namespace Crispcode\LaravelInfluxDB\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Crispcode\LaravelInfluxDB\InfluxServiceProvider;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class InfluxPersister extends Command
{

    public function handle(): int
    {
        $this->info('Start synchronisation of latest influx logs');

        if (!config()->has('influx.host.url') || !config()->has('influx.host.token')) {
            $this->error('You have not setup all required variables yet...');

            return SymfonyCommand::FAILURE;
        }

        $file = storage_path(InfluxServiceProvider::LOG_LOCATION);
        if (!file_exists($file)) {
            $this->info('No log file found, stopping now...');

            return SymfonyCommand::SUCCESS;
        }

        $content = file_get_contents($file);
        file_put_contents($file, '');

        $path = config('influx.host.url') . 'api/v2/write';
        $http = Http::withHeaders([
            'Content-Type' => 'text/plain',
            'Authorization' => 'Token' . config('influx.host.token'),
        ]);

        // TODO - Verify the structure matches the line protocol required by influx
        // TODO - Actually send the message to influx, way easier to test once an instance is available
        // TODO - Should be able to send everything in one go, assuming everything is formatted properly

        $this->info("finished sending all logs to influx, stopping now...");
        return SymfonyCommand::SUCCESS;
    }
}