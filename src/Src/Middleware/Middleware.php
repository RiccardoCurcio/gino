<?php
/**
 * Middleware
 * 
 * PHP version 8
 * 
 * @category Middleware
 * @package  Middleware
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
namespace Gino\Src\Middleware;

use \Gino\Src\Request\Request;

/**
 * Middleware Interface
 * 
 * PHP version 8
 * 
 * @category Middleware
 * @package  Middleware
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
interface Middleware
{
    /**
     * Middleware
     *
     * @param \Gino\Src\Request\Request $request   
     * 
     * @return void
     */
    public static function run(Request $request) : void;
}
