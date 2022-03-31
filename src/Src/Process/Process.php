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
        $logger->debug("AsyncPipeline parent pid:" . posix_getpid());

        $childCount = 0;

        pcntl_signal(SIGCHLD, function($sig) use (&$logger) {
            if ($sig == 9 || $sig == 17) {
                // $childCount--;
                $logger->debug( 'ppkill parent/child');
                exit;
            }
        });
        
       
        $pid = pcntl_fork();

        if (!$pid) {
            $listOfChild = [];
            pcntl_signal(SIGCHLD, function($sig) use (&$childCount, &$logger) {
                if ($sig == 9 || $sig == 17) {
                    // $childCount--;
                    $logger->debug( '--- ' . $childCount);
                    posix_kill(posix_getppid(), SIGKILL);
                    exit;
                }
            });
            array_walk($callbacks, function ($value) use (&$logger, &$input, &$childCount, &$listOfChild) {
                $pidPipeline = pcntl_fork();
                if ($pidPipeline == -1) {
                    $logger->error("AsyncPipeline erroe");
                }
                if ($pidPipeline == 0) {
                    $logger->debug("AsyncPipeline child process pid:" . posix_getpid());
                    array_push($listOfChild, posix_getpid());
                    $childCount = $childCount + 1;
                    $input = $value($input);
                    
                    
                }
                if ($pidPipeline > 0) {
                    pcntl_wait($status);
                }
            });
            array_walk($listOfChild, function($pid) use (&$childCount, &$logger){
                $logger->debug($childCount);
                $childCount = $childCount -1;
                posix_kill($pid, SIGKILL);
                // if ($childCount == 0) {
                //     $logger->debug(posix_getppid());
                //     posix_kill(posix_getppid(), SIGKILL);
                // }
            });
            $info = array();
            pcntl_sigwaitinfo(array(SIGHUP), $info);
        }

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
        $logger = new Logger();
        $logger->debug("SyncPipeline parent pid:" . posix_getpid());
        $listOfChild = [];
        
        pcntl_signal(SIGCHLD, function($sig) {
            if ($sig == 9 || $sig == 17) {
                exit;
            }
        });

        array_walk($callbacks, function ($value) use (&$logger, &$input, &$listOfChild) {
            $pidPipeline = pcntl_fork();
            if ($pidPipeline == -1) {
                $logger->error("SyncPipeline erroe");
            }
            if ($pidPipeline == 0) {
                $logger->debug("syncPipeline child pid:" . posix_getpid());
                array_push($listOfChild, posix_getpid());
                $input = $value($input);
            }
            if ($pidPipeline > 0) {
                pcntl_wait($status, WUNTRACED);
            }
        });

        array_walk($listOfChild, function($pid) {
            posix_kill($pid, SIGKILL);
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

        $pidPipeline = array();
        $pidPipelineInfo = array();
        array_walk($callbacks, function ($value, $key) use (&$logger, &$pidPipeline, &$pidPipelineInfo) {
            $logger->debug($key);
            $pidPipeline[$key] = pcntl_fork();
            if ($pidPipeline[$key] == -1) {
                $logger->error("SyncStorm erroe");
            }
            if ($pidPipeline[$key] == 0) {
                $logger->debug("AsyncStorm child pid:" . posix_getpid());
                $value();
                $pidPipelineInfo[$key] = array();
                pcntl_sigwaitinfo(array(SIGHUP), $pidPipelineInfo[$key]);
            }
        });
    }
}
