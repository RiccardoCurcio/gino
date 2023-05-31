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
use React\Promise\PromiseInterface;
use function React\Promise\Timer\sleep as ReactSleep;

class Async
{
    public static function async(callable $function): callable
    {
        return ReactAsync($function);
    }

    public static function sleep(float $seconds = 0.0): PromiseInterface
    {
       return ReactSleep($seconds);
    }
}
