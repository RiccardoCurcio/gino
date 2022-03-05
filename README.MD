# Gino (ALPHA)
Simple json api framework over swoole for php microservice

## Dependencies
*[open-swoole](https://openswoole.com/)*

## Features
* routing system OOP or Pipeline
    * declare routes
    * use pipeline
* load env file
* resolve dependency injection
* versionig type (URI, QUERYSTRING, CUSTOM_HEADER, ACCEPT_HEADER)
* resolve cors-origin
* Process
    * asyncPipeline
    * syncPipeline
    * asyncStorm 

# DOC
## Env file
### Define env file

```
# SERVER
SERVICE_NAME=gino-app
HOST=127.0.0.1
PORT=8082
CHARSET=utf-8

# Versioning Settings [URI, QUERYSTRING, CUSTOM_HEADER, ACCEPT_HEADER]

# Ex. URI
# VERSIONING_TYPE=URI

# Ex. QUERYSTRING
# VERSIONING_TYPE=QUERYSTRING
# VERSIONING_KEY=version


# Ex. CUSTOM_HEADER
VERSIONING_TYPE=CUSTOM_HEADER
HEADER_KEY=Api-version
VERSIONING_KEY=version

# Ex. ACCEPT_HEADER
# VERSIONING_TYPE=ACCEPT_HEADER
# VERSIONING_ALLOWED_RESPONSE_TYPE=json,xml


# CORSS_ORIGIN
CORSS_ORIGIN_RESOLVE=true
ALLOW_CREDENTIALS=true
ALLOW_ORIGIN=https://gino-app.local
ALLOW_METHODS=POST, GET, PUT, PATCH, DELETE, OPTIONS
ALLOW_HEADERS=Content-Type
MAX_AGE=86400
VARY=Origin
CACHE_CONTROLL=private, must-revalidate
```
## Env
### Load env

```php
Gino\LoadEnv::load('.');
```
## App
### Define app
```php
/**
 * Routing constructor
 *
 * @param array|null $add
 *
 * @return void
 */
public function __construct(?array $add = [])
```

```php
use Gino\Routing;

$app = new Routing(["key" => "value"]);
```
## Routes
### Declare routes
methods : GET POST PUT PATCH DELETE OPTIONS HEAD TRACE CONNECT
```php

.
.
.

$app->get(
    '/v1/example',
    '\App\Controllers\ExampleController'::class,
    'run',
    [\App\Middlewares\ExampleMiddleware::class]
);

$app->post(
    '/v1/example/{parmName}',
    '\App\Controllers\ExampleController'::class,
    'run',
    []
);

```

### Use pipeline

```php
use Gino\Src\Process\Process;
use Gino\Src\Logger\Logger;

.
.
.

$logger = new Logger();

$callback1 = function(mixed $input) use (&$logger)  {
    $logger = new Logger();
    $logger->info("Process CB1 Start");
    $input["data"] = 1;
    $logger->info("Process CB1 Finish");
    return $input;
};

$callback2 = function(mixed $input) use (&$logger)  {
    $logger = new Logger();
    $logger->info("CB2 Start calback");
    $input["data"] = $input["data"] + 1;
    $logger->info("CB2 Finish calback");
    return $input;
};

$callback3 = function(mixed $input) use (&$logger)  {
    $logger = new Logger();
    $logger->info("CB3 Start calback");
    $input["data"] = $input["data"] + 1;
    $logger->info("CB3 Finish calback");
    $input["response"]->response(
        $input["request"],
        [
            "msg" => $input["data"]
        ],
        200
    );
};

$app->get(
    'v1/process',
    Process::class,
    [$callback1, $callback2, $callback3]
);

```
## Server (SWOOLE)
### Init http server
```php
$server = new SwoolServer(getenv("HOST"), getenv("PORT"));
```

### Init https server (http/2)
```php
$server = new SwoolServer(getenv("HOST"), getenv("PORT"), SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
$server->set(
    [
        'ssl_cert_file' => 'ssl.crt',
        'ssl_key_file' => 'ssl.key',
    ]
);
```
#### for local test you can generate key and cert
```bash
$ openssl req -x509 -newkey rsa:4096 -sha256 -days 3650 -nodes \
    -keyout ssl.key -out ssl.crt -subj "/CN=localhost.local" \
    -addext "subjectAltName=DNS:localhost.local.com,DNS:www.localhost.local,IP:127.0.0.1"
```

### Start server
```php
$server->on(
    "start",
    function (SwoolServer $server) {
        $logger = new Logger();
        $logger->info("Server http://" . $server->host . ":" . $server->port);
    }
);

$server->on(
    "request",
    function (SwooleRequest|SwooleRequest2 $request, SwooleResponse|SwooleResponse2 $response) use ($app) {
        $app->run($request, $response);
    }
);

$server->start();
```

## Process
### AsyncPipeline
```php
use Gino\Src\Logger\Logger;
use Gino\Src\Process\Process;

$logger = new Logger();
$callback1 = function(mixed $input) use (&$logger) {
    $logger->info("CB1 Start calback");
    // your code
    $logger->info("CB1 Finish calback");
    return " cb1";
};

$callback2 = function(mixed $input) use (&$logger) {
    $logger->info("CB2 Start calback");
    // your code
    $logger->info("CB2 Finish calback");
    return "cb2";
};

$callback3 = function(mixed $input) use (&$logger) {
    $logger->info("CB3 Start calback");
    // your code
    $logger->info("CB3 Finish calback");
};

Process::asyncPipeline('input', [$callback1, $callback2, $callback3]);
```
### SyncPipeline
```php
use Gino\Src\Logger\Logger;
use Gino\Src\Process\Process;

$logger = new Logger();
$callback1 = function(mixed $input) use (&$logger) {
    $logger->info("CB1 Start calback");
    // your code
    $logger->info("CB1 Finish calback");
    return " cb1";
};

$callback2 = function(mixed $input) use (&$logger) {
    $logger->info("CB2 Start calback");
    // your code
    $logger->info("CB2 Finish calback");
    return "cb2";
};

$callback3 = function(mixed $input) use (&$logger) {
    $logger->info("CB3 Start calback");
    // your code
    $logger->info("CB3 Finish calback");
    return "cb3";
};

$result = Process::syncPipeline('input', [$callback1, $callback2, $callback3]);
```
### AsyncStorm
```php
use Gino\Src\Logger\Logger;
use Gino\Src\Process\Process;

$logger = new Logger();
$callbackStorm1 = function() {
    $logger->info("CB1 Start calback");
    // your code
    $logger->info("CB1 Finish calback");
};

$callbackStorm2 = function() {
    $logger->info("CB2 CB2 Start calback");
    // your code
    $logger->info("CB2 Finish calback");
};

$callbackStorm3 = function() {
    $logger->info("CB3 CB3 Start calback");
    // your code
    $logger->info("CB3 Finish calback");
};

Process::asyncStorm([$callbackStorm1, $callbackStorm2, $callbackStorm3]);
```
## Examples

[gino-examples](https://github.com/RiccardoCurcio/gino-examples)

doc todo
- route tail.*
- cors resolve
- dependencies resolve
- http excepitions
- gino request
- gino response
- xml ?

