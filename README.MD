# Gino
Simple json api framework over swoole

## Dependencies
*[swoole](https://openswoole.com/)*

## Features
* routing system
* load env file
* resolve dependency injection
* versionig type (URI, QUERYSTRING, CUSTOM_HEADER, ACCEPT_HEADER)


## Create example app

```
$ touch composer.json

$ vim composer.json
```

```json
{
    "name": "example/app",
    "type": "project",
    "require": {
        "php": ">=7.3",
        "riccardo-curcio/gino": "@dev"
    },
    "require-dev": {
        "squizlabs/php_codesniffer": "^3.5",
        "swoole/ide-helper": "@dev"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Src\\": "src/"
        }
    },
    "repositories": [
        {
            "type": "path",
            "url": "/PATH/OF/GINO-CODE"
        }
    ]
}
```

## Create struct projecct

```
$ mkdir app
$ mkdir src

touch main.php
vim main.php
```

```php
<?php
/**
 * Main
 *
 * PHP version 8
 *
 * @category Main
 * @package  Main
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
require_once __DIR__ . '/vendor/autoload.php';

use Swoole\HTTP\Server as SwoolServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Gino\Routing;
use Gino\Src\Logger\Logger;

use \App\Routes\HealtCheck\HealtCheck;

Gino\LoadEnv::load('.');


$app = new Routing();

HealtCheck::group($app, 'v1', 'prefixname', [\App\Middlewares\HealtMiddleware::class]);

$server = new SwoolServer(getenv("HOST"), getenv("PORT"));
$server->set(['enable_coroutine' => false]);

$server->on(
    "start",
    function (SwoolServer $server) {
        $logger = new Logger();
        $logger->info("Server http://" . $server->host . ":" . $server->port);
    }
);

$server->on(
    "request",
    function (SwooleRequest $request, SwooleResponse $response) use ($app) {
        $app->run($request, $response);
    }
);

$server->start();
```

## Create routes
```
mkdir app/Routes
mkdir app/Routes/HealtCheck
touch app/Routes/HealtCheck/HealtCheck.php

vim app/Routes/HealtCheck/HealtCheck.php
```

```php
<?php

namespace App\Routes\HealtCheck;

class HealtCheck
{
    public static function group(&$app, ?string $version = 'v1', ?string $prefix = null, ?array $middelwares = null)
    {
        $prefix ? $prefix = '/'. $version . '/' . $prefix : $prefix = '/'. $version;

        $setMiddelwares = array();
        $middelwares ? array_merge($setMiddelwares, $middelwares) : $middelwares = [];
        $controllerGet = '\App\Controllers\\' . $version .'\\\HealtCheckControllers\HealtCheckGetController'::class;
        $controllerPost = '\App\Controllers\\' . $version .'\\\HealtCheckControllers\HealtCheckPostController'::class;

        $app->get(
            $prefix . '/healt',
            $controllerGet,
            'check',
            $middelwares
        );
        
        $app->post(
            $prefix . '/healt',
            $controllerPost,
            'check',
            $middelwares
        );
    }
}

```

### Create middelware

```
mkdir app/Middlewares
touch app/Middlewares/HealtMiddleware.php

vim app/Middlewares/HealtMiddleware.php
```

```php
<?php
namespace App\Middlewares;

use \Gino\Src\Request\Request;
use \Gino\Src\Middleware\Middleware;
use \Gino\Src\Logger\Logger;
use \Gino\Src\Exceptions\HttpExceptions\Unauthorized;

/**
 * Undocumented class
 */
class HealtMiddleware implements Middleware
{
    /**
     * Example Middleware
     *
     * @param Routing\Src\Request\Request $request
     *
     * @return void
     */
    public static function run(Request $request) : void
    {
        $logger = new Logger();
        $logger->info("Middleware check ok");
        $request->set('addFromMiddleware', ["key" => "value"]);
        // throw new Unauthorized('middelware not authorize!', 0);
    }
}

```

### Create Abstruct Controller and Controllers
```
mkdir app/Controllers
mkdir app/Controllers/v1
mkdir app/Controllers/v1/HealtCheckControllers

touch app/Controllers/Controller.php

vim app/Controllers/Controller.php
```
```php
<?php
/**
 * Controller
 * 
 * PHP version 8
 * 
 * @category AbstractController
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
namespace App\Controllers;

/**
 * Abstract controller
 * 
 * @category AbstractController
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
abstract class Controller
{

}
```
```
touch app/Controllers/v1/HealtCheckControllers/HealtCheckGetController.php

vim app/Controllers/v1/HealtCheckControllers/HealtCheckGetController.php
```
```php
<?php
/**
 * Controller healtcheck
 *
 * PHP version 8
 *
 * @category Controller
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace App\Controllers\v1\HealtCheckControllers;

use App\Controllers\Controller;
use \Gino\Src\Request\Request;
use \Gino\Src\Response\Response;

use \Psr\Log\LoggerInterface;

/**
 * Controller healtcheck
 *
 * @category Controller
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class HealtCheckGetController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $logger;


    /**
     * Costructor HealtCheckGetController
     *
     * @param \Psr\Log\LoggerInterface                                                  $logger
     *
     * @dependency Gino\Src\Logger\Logger                                           $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Check methods
     *
     * @param \Gino\Src\Request\Request  $request
     * @param \Gino\Src\Response\Response $response
     *
     * @return void
     */
    public function check(Request $request, Response $response)
    {
        $this->logger->info("HealtCheckGetController v1");

        $response->response(
            [
                "msg" => ["example" => "check get"]
            ],
            200,
            $request
        );
    }
}

```

```
touch app/Controllers/v1/HealtCheckControllers/HealtCheckPostController.php

vim app/Controllers/v1/HealtCheckControllers/HealtCheckPostController.php
```

```php
<?php
/**
 * Controller healtcheck
 *
 * PHP version 8
 *
 * @category Controller
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace App\Controllers\v1\HealtCheckControllers;

use App\Controllers\Controller;
use \Gino\Src\Request\Request;
use \Gino\Src\Response\Response;

use Src\Application\Adapters\v1\HealtCheckAdapters\HealtCheckAdapterInterface;
use Src\Domain\Services\v1\HealtCheckServices\HealtCheckServiceInterface;

use \Psr\Log\LoggerInterface;

/**
 * Controller healtcheck
 *
 * @category Controller
 * @package  Controllers
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class HealtCheckPostController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $logger;


    /**
     * Costructor HealtCheckPostController
     *
     * @param \Psr\Log\LoggerInterface                                                  $logger
     *
     * @dependency Gino\Src\Logger\Logger                                           $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Check methods
     *
     * @param \Gino\Src\Request\Request  $request
     * @param \Gino\Src\Response\Response $response
     *
     * @return void
     */
    public function check(Request $request, Response $response)
    {
        $this->logger->info("HealtCheckPostController v1");
        $response->response(
            [
                "msg" => ["example" => "check post"]
            ],
            201,
            $request
        );
    }
}

```

## create env

```
$ touch .env

vim .env
```

```env
# SERVER
HOST=127.0.0.1
PORT=8083
CHARSET=utf-8

# Versioning Settings [URI, QUERYSTRING, CUSTOM_HEADER, ACCEPT_HEADER]

# Ex. URI
# VERSIONING_TYPE=URI

# Ex. QUERYSTRING
# VERSIONING_TYPE=QUERYSTRING
# VERSIONING_KEY=version


# Ex. CUSTOM_HEADER
# VERSIONING_TYPE=CUSTOM_HEADER
# HEADER_KEY=Api-version
# VERSIONING_KEY=version

# Ex. ACCEPT_HEADER
VERSIONING_TYPE=ACCEPT_HEADER
VERSIONING_ALLOWED_RESPONSE_TYPE=json,xml
```

## Run
```
$ composer install
$ php main.php
```

## Requests example (ACCEPT_HEADER)
```
GET http://localhost:8083/prefixname/healt

headers
    application/vnd.localhost.v1+json
```
```
POST http://localhost:8083/prefixname/healt

headers
    application/vnd.localhost.v1+json
    Content-Type: application/json

body
    {
	    "data": "example"
    }
```

## Requests example (URI)
```
GET http://localhost:8083/v1/prefixname/healt

```
```
POST http://localhost:8083/v1/prefixname/healt

headers
    Content-Type: application/json

body
    {
	    "data": "example"
    }
```

## Requests example (QUERYSTRING)
```
GET http://localhost:8083/prefixname/healt?version=1

```
```
POST http://localhost:8083/prefixname/healt?version=1

headers
    Content-Type: application/json

body
    {
	    "data": "example"
    }
```


## Requests example (CUSTOM_HEADER)
```
GET http://localhost:8083/prefixname/healt

headers
    Api-version: version=1
```
```
POST http://localhost:8083/prefixname/healt

headers
    Content-Type: application/json
    Api-version: version=1

body
    {
	    "data": "example"
    }
```