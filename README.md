## Configuration Options

| Environment Key     | Configuration Key      | Default | Description                                                                                           |
|---------------------|------------------------|---------|-------------------------------------------------------------------------------------------------------|
| `INFLUXDB_SERVER`   | `influxdb.server`      |         | (Required) The InfluxDB server to write logs to.                                                      |
| `INFLUXDB_TOKEN`    | `influxdb.token`       |         | (Required) The access token for your specified server.                                                |
| `INFLUXDB_UDP_PORT` | `influxdb.upd_port`    | `8089`  | Which      port hould be used for the UDP writer. If you don't use this writer, it may be left empty. |
|                     | `influxdb.client_opts` | `[]`    | Any extra configuration you'd like to pass to the Guzzle HTTP client.                                 |
