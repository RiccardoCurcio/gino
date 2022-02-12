<?php
/**
 * CorsController
 *
 * PHP version 8
 *
 * @category CorsController
 * @package  CorsController
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace Gino\Src\CorsController;


use \Gino\Src\Request\Request;
use \Gino\Src\Response\Response;
use \Psr\Log\LoggerInterface;


/**
 * CorsController
 *
 * @category CorsController
 * @package  CorsController
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class CorsController
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $logger;

     /**
     * Costructor Cors
     *
     * @param \Psr\Log\LoggerInterface                                                  $logger
     *
     * @dependency Gino\Src\Logger\Logger                                           $logger
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * resolve methods
     *
     * @param \Gino\Src\Request\Request  $request
     * @param \Gino\Src\Response\Response $response
     *
     * @return void
     */
    public function resolve(Request $request, Response $response)
    {
        $this->logger->info("Resolve cors");
        $corsHeaders = [
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

        $response->response(
            $request,
            [],
            204,
            $corsHeaders
        );
    }
}
