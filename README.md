## Configuration Options

| Environment Key | Configuration Key      | Default | Description                                                           |
|-----------------|------------------------|---------|-----------------------------------------------------------------------|
| `INFLUX_SERVER` | `influxdb.server`      |         | (Required) The InfluxDB server to write logs to.                      |
| `INFLUX_TOKEN`  | `influxdb.token`       |         | (Required) The access token for your specified server.                |
|                 | `influxdb.client_opts` | `[]`    | Any extra configuration you'd like to pass to the Guzzle HTTP client. |
