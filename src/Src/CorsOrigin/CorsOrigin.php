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
    public static function resolveCors($app) {
        
        $app->options(
            "/{tail.*}/{suca}",
            '\Gino\Src\CorsController\CorsController'::class,
            'resolve',
            []
        );   
    }
}