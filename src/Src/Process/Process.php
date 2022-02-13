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
     * Create a child process
     *
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function start(string $name = "", callable $callback) {
       
        $logger = new Logger();
        $logger->info("Parent process parent pid:" .posix_getpid());

        $pid = pcntl_fork();
        
        if (!$pid) {
            $logger = new Logger();
            $logger->info("Child process " . $name . " child pid:" .posix_getpid());
            $callback();
            $info = array();
            pcntl_sigwaitinfo(array(SIGHUP), $info);
        }

       
    }
}