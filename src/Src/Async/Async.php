<?php

declare(ticks=1);
/**
 * Async
 *
 * PHP version 8
 *
 * @category Async
 * @package  Async
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\Async;


require_once __DIR__ . '/../../../vendor/autoload.php';

use function React\Async\async as ReactAsync;

class Async
{
    public static function async(callable $function): callable
    {
        return ReactAsync($function);
    }
}
