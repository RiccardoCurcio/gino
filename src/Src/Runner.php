<?php
/**
 * Runner
 *
 * PHP version 8
 *
 * @category Runner
 * @package  Runner
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace Gino\Src;

use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http2\Request as SwooleRequest2;
use Swoole\Http2\Response as SwooleResponse2;
use Gino\Src\Request\Request;
use Gino\Src\Response\Response;

/**
 * Runner trait
 *
 * @category Runner
 * @package  Runner
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
trait Runner
{
    use \Gino\Src\Matcher;
    
    /**
     * Run method
     *
     * @param SwooleRequest  $httpRequest
     * @param SwooleResponse $httpResponse
     *
     * @return void
     */
    public function run(
        SwooleRequest|SwooleRequest2 $httpRequest,
        SwooleResponse|SwooleResponse2 $httpResponse
    ) : void {
        $response = new Response($httpResponse);
        try {
            $worker = Matcher::match($httpRequest, $this->routes);
            $worker ? $worker['request']->set('add', $this->add) : null;
            $this->middelwareRun($worker['middlewares'], $worker['request']);
            $worker['class']->{$worker['method']}($worker['request'], $response);
        } catch (\Exception $ex) {
            $corsHeader = [];

            if (filter_var(getenv("CORSS_ORIGIN_RESOLVE"), FILTER_VALIDATE_BOOLEAN)) {
                $corsHeader =  [
                    "access-control-allow-credentials" => getenv("ALLOW_CREDENTIALS") ? getenv("ALLOW_CREDENTIALS") : "true",
                    "access-control-allow-origin" => getenv("ALLOW_ORIGIN") ? getenv("ALLOW_ORIGIN") : "*",
                    "access-control-allow-methods" => getenv("ALLOW_METHODS") ? getenv("ALLOW_METHODS") : "*",
                    "access-control-allow-headers" => getenv("ALLOW_HEADERS") ? getenv("ALLOW_HEADERS") : "*",
                    "access-control-max-age" => getenv("MAX_AGE") ? getenv("MAX_AGE") : "0",
                    "access-control-expose-headers" => " ",
                    "Server" => getenv("SERVICE_NAME") ? getenv("SERVICE_NAME") : "gino-app",
                    "vary" => getenv("VARY") ? getenv("VARY") : "Origin",
                    "cache-controll" => getenv("CACHE_CONTROLL") ? getenv("CACHE_CONTROLL") : "private, must-revalidate"
                ];
            }
            $response->json(
                [
                    "msg" => $ex->__toString()
                ],
                method_exists($ex, 'statusCode') ? $ex->{'statusCode'}() : 500,
                "application/json",
                getenv('CHARSET') == false ? null : getenv('CHARSET'),
                $corsHeader
            );
        }
    }

    /**
     * Run middlewares
     *
     * @param array $middlewares
     * @param Gino\Src\Request\Request $request
     *
     * @return void
     */
    private function middelwareRun(array $middlewares, Request $request) : void
    {
        foreach ($middlewares as $middleware) {
            if (new $middleware() instanceof \Gino\Src\Middleware\Middleware) {
                $middleware::run($request);
            }
        }
    }
}
