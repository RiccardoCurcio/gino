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
* versioning type (URI, QUERYSTRING, CUSTOM_HEADER, ACCEPT_HEADER)
* resolve cross-origin

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


# CROSS_ORIGIN
CROSS_ORIGIN_RESOLVE=true
ALLOW_CREDENTIALS=true
ALLOW_ORIGIN=https://gino-app.local
ALLOW_METHODS=POST, GET, PUT, PATCH, DELETE, OPTIONS
ALLOW_HEADERS=Content-Type
MAX_AGE=86400
VARY=Origin
CACHE_CONTROL=private, must-revalidate
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

