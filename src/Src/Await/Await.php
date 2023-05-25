<?php

declare(ticks=1);
/**
 * Await
 *
 * PHP version 8
 *
 * @category Await
 * @package  Await
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\Await;


require_once __DIR__ . '/../../../vendor/autoload.php';

use function React\Async\await as ReactAwait;
use React\Promise\PromiseInterface;

class Await
{
    public static function await(PromiseInterface $promise): mixed {
        return ReactAwait($promise);
    }
    
}
