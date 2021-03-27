<?php
/**
 * Logger
 *
 * PHP version 8
 *
 * @category Logger
 * @package  Logger
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace Gino\Src\Logger;

use \Psr\Log\AbstractLogger;

/**
 * Logger
 *
 * PHP version 8
 *
 * @category Logger
 * @package  Logger
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Logger extends AbstractLogger
{

    /**
     * Costructor Logger
     */
    public function __construct()
    {
        // --
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        if (gettype($message) == "object") {
            $message = (array) $message;
        }

        if (gettype($message) == "array") {
            $message = json_encode(
                $message,
                JSON_UNESCAPED_LINE_TERMINATORS |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS
            );
        }

        
        date_default_timezone_set('UTC');
        $date = date('Y-m-d h:i:s');
        // php://stderr
        $STDOUT = fopen("php://stdout", "w");
        fwrite(
            $STDOUT,
            '[' . $date . '] ' . strtoupper($level) . ' ' . (string) $message. "\n"
        );
        fclose($STDOUT);
    }
}
