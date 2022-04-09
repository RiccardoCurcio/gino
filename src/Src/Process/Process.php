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
class Process
{

    /**
     * Create async pipeline // callable $callback
     *
     * @param mixed $input
     * @param callable $callback
     * @return void
     */
    public static function asyncPipeline(mixed $input = null, array $callbacks): void
    {

        $logger = new Logger();
        $logger->debug("AsyncPipeline parent pid:" . posix_getpid() . " - " . posix_getppid());
        
        pcntl_signal(SIGCHLD, function($sig) use ($logger) {
            $logger->error(" HANDLER #### ");
            if ($sig == 9 || $sig == 17) {
                
                // try {
                //     exit(1);
                // } catch (\Exception) {
                //     // --
                // }
                exit;
            }
           
        }, true);

        $status = function($gparent) use (&$logger) {
            $logger->debug("SyncPipeline complete ". $gparent);
            posix_kill($gparent, SIGKILL);
            pcntl_signal_dispatch();
        };


        $pidPipeline = pcntl_fork();
        if ($pidPipeline == -1) {
            $logger->error("SyncPipeline erroe");
        }

        if ($pidPipeline == 0) {
           
            $logger->debug("AsyncPipeline child (parent) pid:" . posix_getpid());
            $gpid = posix_getpid();
            Process::syncPipeline($input, $callbacks, $status, $gpid);
            // exit;
        }

        if ($pidPipeline > 0) {
            pcntl_wait($status, WUNTRACED);
        }


    }

    /**
     * Create sync pipeline // callable $callback
     *
     * @param string $name
     * @param array $callbacks
     * @return mixed
     */
    public static function syncPipeline(mixed $input = null, array $callbacks, ?callable &$conpleteCallback = null, ?int $gparent=null): mixed
    {
        $logger = new Logger();
        $logger->debug("SyncPipeline parent pid:" . posix_getpid());
        $listOfChild = [];
        
        pcntl_signal(SIGCHLD, function($sig) use (&$conpleteCallback, $gparent, $logger) {
            $logger->error("$$$$$$$$ HANDLER #### ");
            if ($sig == 9 || $sig == 17) {
                if ($conpleteCallback) {
                    $conpleteCallback($gparent);
                }
                
            }
            exit(1);
        }, TRUE);

        array_walk($callbacks, function ($callback) use (&$logger, &$input, &$listOfChild) {
            $pidPipeline = pcntl_fork();
            if ($pidPipeline == -1) {
                $logger->error("SyncPipeline erroe");
            }
            if ($pidPipeline == 0) {
                $logger->debug("syncPipeline child pid:" . posix_getpid());
                array_push($listOfChild, posix_getpid());
                $input = $callback($input);
            }
            if ($pidPipeline > 0) {
                pcntl_wait($status, WUNTRACED);
            }
        });

        array_walk($listOfChild, function($pid) {
            posix_kill($pid, SIGKILL);
            pcntl_signal_dispatch();
        });
        
        return $input;
    }

    /**
     * Storm of async process
     *
     * @param array $callbacks
     * @return void
     */
    public static function asyncStorm(array $callbacks): void
    {
        $logger = new Logger();
        $logger->debug("AsyncStorm parent pid:" . posix_getpid());

        pcntl_signal(SIGCHLD, function($sig) use ($logger) {
            $logger->debug("sig " . $sig);
            if ($sig == 9 || $sig == 17 || $sig == 15) {
                exit(1);
            }
        });

        $pidPipeline = array();
        array_walk($callbacks, function ($callback, $key) use (&$logger, &$pidPipeline) {
            $pidPipeline[$key] = pcntl_fork();
            if ($pidPipeline[$key] == -1) {
                $logger->error("SyncStorm erroe");
            }
            if ($pidPipeline[$key] == 0) {
                $logger->debug("AsyncStorm child pid:" . posix_getpid());
                $callback();
                posix_kill(posix_getpid(), SIGKILL);
                pcntl_signal_dispatch();
            }
        });
    }
}
