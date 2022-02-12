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
use Swoole\Http2\Request as SwooleRequest2;

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
     * @param \Swoole\Http\Request|SwooleRequest2   $httpRequest
     * @param array                                 $routes
     *
     * @return array|null
     */
    public static function match(SwooleRequest|SwooleRequest2 $httpRequest, array $routes) : ?array
    {
        $request = new request();
        $request->set("gino-request-code", uniqid('GINO-', true));
        $request->set("swoole", $httpRequest);

        Matcher::setHeaders($request, $httpRequest->header);
        Matcher::setQueryString($request, $httpRequest->get);
        $uri = Matcher::setVersion($request, $httpRequest->server["request_uri"]);
        
        foreach ($routes[$httpRequest->getMethod()] as $value) {
            $regex = str_replace("/", "\\/", $value["uri"]);
            
            while (preg_match("/{.*?}/m", $regex)) {
                $regex = preg_replace("/{tail.*}/m", "*", $regex);
                $regex = preg_replace("/{.*?}/m", "[^?]*", $regex);
            }
            
            $regex = "/".$regex."$/m";
            
            if (preg_match_all($regex, $uri, $matches, PREG_SET_ORDER)) {
                Matcher::setParam(
                    $request,
                    $value["uri"],
                    $uri
                );
                
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
     * @param string                    $routeUri
     * @param string                    $requestUri
     *
     * @return void
     */
    public static function setParam(
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
     * @param array                     $header
     *
     * @return void
     */
    public static function setHeaders(Request $request, array $header) : void
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
     * @param array|null                       $get
     *
     * @return void
     */
    public static function setQueryString(Request $request, ?array $get) : void
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
     * @param \Gino\Src\Request\Request     $request
     * @param SwooleRequest|SwooleRequest2  $httpRequest
     *
     * @return void
     */
    public static function setBody(Request $request, SwooleRequest|SwooleRequest2 $httpRequest)
    {
        switch ($request->get('Content-type')) {
            case 'application/json':
                $request->set('body', json_decode($httpRequest->getContent()));
                break;
            default:
                $request->set('body', $httpRequest->getContent());
        }
    }

    /**
     * set versioning type
     *
     * @param \Gino\Src\Request\Request $request
     * @param string $uri
     * @return string
     */
    public static function setVersion(Request $request, string $uri): string
    {
        switch (getenv('VERSIONING_TYPE', 'URI')) {
            case 'QUERYSTRING':
                $uri = '/v' . $request->get(getenv('VERSIONING_KEY', 'version')) . $uri;
                break;
            case 'CUSTOM_HEADER':
                $key = str_replace(
                    ' ',
                    '-',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', getenv('HEADER_KEY', 'Api-version'))
                        )
                    )
                );
                $header = explode("=", $request->get(getenv('HEADER_KEY', 'Api-Version')));
                $uri = $header[0] === getenv('VERSIONING_KEY', 'version') ? '/v' . $header[1] . $uri : $uri;
                break;
            case 'ACCEPT_HEADER':
                $accept = $request->get('Accept');
                $service = 'application/vnd.'. explode(":", $request->get('Host'))[0];
                $versionType = Matcher::strstrAfter($accept, $service . '.');
                $uri =  '/' . $versionType['version'] . $uri;

                in_array(
                    $versionType['response-content-type'],
                    explode(',', getenv('VERSIONING_ALLOWED_RESPONSE_TYPE'))
                ) ?
                $request->set(
                    'response-content-type',
                    [
                        "type" => $versionType['response-content-type'],
                        "content-type" => $accept
                    ]
                ) :
                throw new NotFound('Route not found!', 0);
                break;
            default:
                // default is uri type
        }
        return $uri;
    }

    /**
     * After str
     *
     * @param string $haystack
     * @param string $needle
     * @param boolean $case_insensitive
     * @return array|null
     */
    public static function strstrAfter(string $haystack, string $needle, $case_insensitive = true): ?array
    {
        $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
        $pos = $strpos($haystack, $needle);
        if (is_int($pos)) {
            $sub = explode('+', substr($haystack, $pos + strlen($needle)));
            return ['version' => $sub[0], 'response-content-type' => $sub[1]];
        }
        return null;
    }
}
