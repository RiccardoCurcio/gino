<?php

declare(ticks=1);
/**
 * Process
 *
 * PHP version 8
 *
 * @category Process
 * @package  Process
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\Process;

use \Gino\Src\Async\Async;
use \Gino\Src\Await\Await;

/**
 * Process class
 *
 * @category Process
 * @package  Process
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Process
{

    /**
     * Create async pipeline // callable $callback
     *
     * @param mixed $input
     * @param callable $callback
     * @param callable | null $onFulfilled
     * @param callable | null $onRejected
     * @return void
     */
    public static function asyncPipeline(mixed $input = null, array $callbacks, ?callable $onFulfilled = null, ?callable $onRejected = null): void
    {

        $runner = Async::async(function () use ($input, $callbacks): mixed {
            $result = Process::syncPipeline($input, $callbacks);
            return $result;
        })();

        $runner->then($onFulfilled, $onRejected);
    }

    /**
     * Create sync pipeline // callable $callback
     *
     * @param string $name
     * @param array $callbacks
     * @return mixed
     */
    public static function syncPipeline(mixed $input = null, array $callbacks): mixed
    {
        $promises = [];

        array_walk($callbacks, function ($callback) use (&$promises) {
            array_push($promises, Async::async($callback));
        });

        array_walk($promises, function ($promise) use (&$input) {
            $input = Await::await($promise($input));
        });

        return $input;
    }

    /**
     * Storm of async process
     *
     * @param array $callbacks
     * @return void
     */
    // public static function asyncStorm(array $callbacks): void
    // {
    //     $logger = new Logger();
    //     $logger->debug("AsyncStorm parent pid:" . posix_getpid());

    //     pcntl_signal(SIGCHLD, function($sig) use ($logger) {
    //         $logger->debug("sig " . $sig);
    //         if ($sig == 9 || $sig == 17 || $sig == 15) {
    //             exit(1);
    //         }
    //     });

    //     $pidPipeline = array();
    //     array_walk($callbacks, function ($callback, $key) use (&$logger, &$pidPipeline) {
    //         $pidPipeline[$key] = pcntl_fork();
    //         if ($pidPipeline[$key] == -1) {
    //             $logger->error("SyncStorm erroe");
    //         }
    //         if ($pidPipeline[$key] == 0) {
    //             $logger->debug("AsyncStorm child pid:" . posix_getpid());
    //             $callback();
    //             posix_kill(posix_getpid(), SIGKILL);
    //             pcntl_signal_dispatch();
    //         }
    //     });
    // }
}
