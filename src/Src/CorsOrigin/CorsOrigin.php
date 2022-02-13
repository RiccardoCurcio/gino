<?php
/**
 * CorsOrigin
 *
 * PHP version 8
 *
 * @category CorsOrigin
 * @package  CorsOrigin
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\CorsOrigin;

use Gino\Routing;


/**
 * CorsOrigin trait
 *
 * @category CorsOrigin
 * @package  CorsOrigin
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
trait CorsOrigin
{
    /**
     * Add route for prefiht request
     *
     * @param [type] $app
     * @return void
     */
    public static function resolveCors(Routing $app) {
        
        $app->options(
            "/{tail.*}",
            '\Gino\Src\CorsController\CorsController'::class,
            'resolve',
            []
        );   
    }
}