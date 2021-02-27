<?php
/**
 * Response object
 * 
 * PHP version 8
 * 
 * @category HttpResponse
 * @package  Response
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */

namespace Gino\Src\Response;

use Swoole\Http\Response as SwooleResponse;

/**
 * Response class
 * 
 * @category HttpResponse
 * @package  Response
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com 
 */
class Response
{
    /**
     * Swoole response
     *
     * @var Swoole\Http\Response 
     */
    private $_response;

    /**
     * Costructor of Response class
     * 
     * @param SwooleResponse $swooleResponse 
     * 
     * @return void
     */
    function __construct(SwooleResponse $swooleResponse)
    {
        $this->_response = $swooleResponse;
    }

    /**
     * Set ContentType
     *
     * @param string $contentType 
     * 
     * @return void
     */
    private function _setHeaderContentType(string $contentType) : void
    {
        $this->_response->header("Content-Type", $contentType);
    }

    /**
     * Set respomse status code
     *
     * @param int $code 
     * 
     * @return void
     */
    private function _setResponeStatusCode(int $code) : void
    {
        $this->_response->status($code);
    }
    
    /**
     * Generate json response
     *
     * @param array $content 
     * @param int   $code 
     * 
     * @return void
     */
    public function json(array $content, int $code) : void
    {
        $this->_setHeaderContentType("application/json");
        $this->_setResponeStatusCode($code);
        $this->_response->end(
            json_encode(
                $content, 
                JSON_UNESCAPED_LINE_TERMINATORS | 
                JSON_UNESCAPED_UNICODE | 
                JSON_UNESCAPED_SLASHES | 
                JSON_HEX_TAG | 
                JSON_HEX_AMP | 
                JSON_HEX_APOS
            )
        );
    }
}
