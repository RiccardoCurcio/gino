<?php
/**
 * Matcher
 * 
 * PHP version 8
 * 
 * @category Matcher
 * @package  Matcher
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
namespace Gino\Src;

use \Gino\Src\Exceptions\HttpExceptions\NotFound;
use Swoole\Http\Request as SwooleRequest;
use \Gino\Src\Request\Request;

/**
 * Matcher trait
 * 
 * @category Matcher
 * @package  Matcher
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
trait Matcher
{
    

    /**
     * Match request
     *
     * @param \Swoole\Http\Request $httpRequest 
     * @param array                $routes 
     * 
     * @return array|null
     */   
    static public function match(SwooleRequest $httpRequest, array $routes) : ?array
    {
        $uri = $httpRequest->server["request_uri"];
        foreach ($routes[$httpRequest->getMethod()] as $value) {
            $regex = str_replace("/", "\\/", $value["uri"]);
            
            while (preg_match("/{.*?}/m", $regex)) {
                $regex = preg_replace("/{.*?}/m", "[^?]*", $regex);
            }

            $regex = "/".$regex."$/m";
            
            if (preg_match_all($regex, $uri, $matches, PREG_SET_ORDER)) {
                $request = new request();
                $request->set("swoole", $httpRequest);

                Matcher::setParam(
                    $request,
                    $value["uri"],
                    $uri
                );
                Matcher::setHeaders($request, $httpRequest->header);
                Matcher::setQueryString($request, $httpRequest->get);
                Matcher::setBody($request, $httpRequest);
                return [
                    "class" => $value["className"],
                    "method" => $value["method"],
                    "middlewares" => $value["middlewares"],
                    "request" => $request
                ];
            }
        }
        throw new NotFound('Route not found!', 0);
        return null;
    }

    /**
     * Set param from uri
     *
     * @param \Gino\Src\Request\Request $request 
     * @param string                       $routeUri 
     * @param string                       $requestUri 
     * 
     * @return void
     */
    static public function setParam(
        Request $request,
        string $routeUri,
        string $requestUri
    ) : void {
        $regex = "/{(.*?)}/m";
        $routeSplit = explode('/', $routeUri);
        $uriSplit = explode('/', $requestUri);
        foreach ($routeSplit as $key => $value) {
            if (preg_match_all($regex, $value, $matches, PREG_SET_ORDER) > 0) {
                $request->set($matches[0][1], $uriSplit[$key]);
            }
        }
    }

    /**
     * Set headers from request
     *
     * @param \Gino\Src\Request\Request $request 
     * @param array                        $header 
     * 
     * @return void
     */
    static public function setHeaders(Request $request, array $header) : void
    {
        foreach ($header as $key => $value) {
            $request->set(
                str_replace(
                    ' ',
                    '-',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', $key)
                        )
                    )
                ),
                $value
            );
        }
    }

    /**
     * Set query string
     *
     * @param @param \Gino\Src\Request\Request $request 
     * @param array|null                          $get 
     * 
     * @return void
     */
    static public function setQueryString(Request $request, ?array $get) : void
    {
        if ($get) {
            foreach ($get as $key => $value) {
                $request->set($key, $value);
            }
        }
        
    }

    /**
     * Set body content
     *
     * @param \Gino\Src\Request\Request $request 
     * @param SwooleRequest                $httpRequest 
     * 
     * @return void
     */
    static public function setBody(Request $request, SwooleRequest $httpRequest)
    {
        switch ($request->get('Content-type')) {
        case 'application/json':
            $request->set('body', json_decode($httpRequest->getContent()));
            break;
        default;
            $request->set('body', $httpRequest->getContent());
        }
        
    }
}