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
use \Gino\Src\CorsHeaders\CorsHeaders;

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
     * @param \Psr\Log\LoggerInterface      $logger
     *
     * @dependency Gino\Src\Logger\Logger   $logger
     */
    public function __construct(LoggerInterface $logger)
    {
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

        $corsHeaders = CorsHeaders::getCorsHeaders();
        
        $response->response(
            $request,
            [],
            204,
            $corsHeaders
        );
    }
}
