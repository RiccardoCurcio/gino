<?php
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

use Gino\Src\Logger\Logger;

/**
 * Process class
 *
 * @category Process
 * @package  Process
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Process {

    /**
     * Create a child process // callable $callback
     *
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function asyncPipeline(mixed $input = null, array $callbacks) {
       
        $logger = new Logger();
        $logger->info("Parent process parent pid:" .posix_getpid());

        $pid = pcntl_fork();
        
        if (!$pid) {
            array_walk($callbacks, function($value) use (&$logger, &$input){
                $pidPipeline = pcntl_fork();
                if ($pidPipeline == -1) {
                    $logger->error("Pipeline erroe");
                }
                if ($pidPipeline == 0) {
                    $logger->info("Child process pipeline child pid:" .posix_getpid());
                    $input = $value($input);
                }
                if ($pidPipeline > 0) {
                    pcntl_wait($status);
                }
            });
            $info = array();
            pcntl_sigwaitinfo(array(SIGHUP), $info);
        }

       
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $callbacks
     * @return void
     */
    public static function syncPipeline(string $name = "", array $callbacks) {}

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $callbacks
     * @return void
     */
    public static function asyncStorm(string $name = "", array $callbacks) {}

    private static function run(&$fn, &$return) {
        $return = $fn();
    }
}