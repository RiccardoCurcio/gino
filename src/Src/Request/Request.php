<?php
/**
 * Request object
 *
 * PHP version 8
 *
 * @category HttpRequest
 * @package  Request
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\Request;

/**
 * Request class
 *
 * @category HttpRequest
 * @package  Request
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Request
{
    /**
     * List of parameters
     *
     * @var array
     */
    private $params;

    /**
     * Costructor of request class
     *
     * @return void
     */
    public function __construct()
    {
        $this->params = [];
    }
    /**
     * Set new param fron a http request
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $name, $value) : void
    {
        $this->params[$name] = $value;
    }

    /**
     * Get parameter from name
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function get(string $name)
    {
        return $this->params[$name] ?? null;
    }
}
