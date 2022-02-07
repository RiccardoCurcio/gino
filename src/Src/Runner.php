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
        SwooleRequest $httpRequest,
        SwooleResponse $httpResponse
    ) : void {
        $response = new Response($httpResponse);
        try {
            $worker = Matcher::match($httpRequest, $this->routes);
            $worker ? $worker['request']->set('add', $this->add) : null;
            $this->middelwareRun($worker['middlewares'], $worker['request']);
            $worker['class']->{$worker['method']}($worker['request'], $response);
        } catch (\Exception $ex) {
            $response->json(
                [
                    "msg" => $ex->__toString()
                ],
                method_exists($ex, 'statusCode') ? $ex->{'statusCode'}() : 500
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
