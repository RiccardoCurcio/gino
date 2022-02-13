<?php
/**
 * CorsHeaders
 *
 * PHP version 8
 *
 * @category CorsHeaders
 * @package  CorsHeaders
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\CorsHeaders;




/**
 * CorsHeaders trait
 *
 * @category CorsHeaders
 * @package  CorsHeaders
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
trait CorsHeaders
{
    /**
     * Cors headers array
     *
     * @return array
     */
    public static function getCorsHeaders() : array {
        
        return [
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
}