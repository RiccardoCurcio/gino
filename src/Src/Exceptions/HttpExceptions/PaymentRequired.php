<?php
/**
 * HttpExceptions
 * 
 * PHP version 8
 * 
 * @category HttpExceptions
 * @package  HttpExceptions
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
namespace Gino\Src\Exceptions\HttpExceptions;

/**
 * PaymentRequired
 * 
 * PHP version 8
 * 
 * @category HttpExceptions
 * @package  HttpExceptions
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
class PaymentRequired extends \Exception
{
    /**
     * Costructor
     *
     * @param string     $message 
     * @param integer    $code 
     * @param \Throwable $previous 
     */
    public function __construct(
        $message = 'Not found',
        $code = 0,
        \Throwable $previous = null
    ) {    
        parent::__construct($message, $code, $previous);
    }

    /**
     * Override parent __toString()
     *
     * @return string
     */
    public function __toString() : string 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    /**
     * Http status code
     *
     * @return integer
     */
    public function statusCode() : int 
    {
        return 402;
    }

}