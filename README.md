## Quickstart

1. Install the package
```shell
composer require crispcode/laravel-influx-db
```
2. Extend your environment file
```text
INFLUXDB_SERVER="http://your.influx.server:8086"
INFLUXDB_TOKEN="RatherLongStringGoesHere"
INFLUXDB_LOGS_ORGANIZATION="CrispCode"
INFLUXDB_LOGS_BUCKET="laravel-logs"
```
3. Log to your heart's content
```php
Log::channel('influxdb')->info('Hello from Laravel!');
```

## Configuration Options

| Environment Key         | Configuration Key       | Default | Description                                                                                                                                                                   |
|-------------------------|-------------------------|---------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `INFLUXDB_SERVER`       | `influxdb.server`       |         | (Required) The URL of the InfluxDB server to write logs to.                                                                                                                   |
| `INFLUXDB_TOKEN`        | `influxdb.token`        |         | (Required) The access token for your specified server.                                                                                                                        |
| `INFLUXDB_BUCKET`       | `influxdb.bucket`       |         | Defines to which buckets information should be written.                                                                                                                       |
| `INFLUXDB_ORGANIZATION` | `influxdb.organization` |         | Defines to which organization information should be written.                                                                                                                  |
| `INFLUXDB_UDP_PORT`     | `influxdb.upd_port`     | `8089`  | Which port should be used for the UDP writer. If you don't use this writer, it may be left empty.                                                                             |
|                         | `influxdb.client_opts`  | `[]`    | Any extra configuration you'd like to pass to the Guzzle HTTP client, see the [Guzzle docs](https://docs.guzzlephp.org/en/stable/request-options.html) for available options. |

### Logging Configuration

| Environment Key              | Configuration Key                             | Default                           | Description                                             |
|------------------------------|-----------------------------------------------|-----------------------------------|---------------------------------------------------------|
| `INFLUXDB_LOGS_BUCKET`       | `logging.channels.influxdb.with.bucket`       | `config('influxdb.bucket')`       | The bucket to which logs should be written.             |
| `INFLUXDB_LOGS_MEASUREMENT`  | `logging.channels.influxdb.with.measurement`  | `'logs'`                          | The measurement stream to which logs should be written. |
| `INFLUXDB_LOGS_ORGANIZATION` | `logging.channels.influxdb.with.organization` | `config('influxdb.organization')` | The organization to which logs should be written.       |
