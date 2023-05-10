<?php
/**
 * Routes
 *
 * PHP version 8
 *
 * @category Routes
 * @package  Routes
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
namespace Gino\Src;


use Gino\Src\Process\Process;

/**
 * Routes trait
 *
 * @category Routes
 * @package  Routes
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
trait Routes
{
    use \Gino\Src\DependencyInjection\DependencyInjection;
    /**
     * Set GET route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function get(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        
        array_push(
            $this->routes["GET"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set POST route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function post(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["POST"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set PUT route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function put(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["PUT"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set PATCH route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function patch(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["PATCH"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set DELETE route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function delete(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["DELETE"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set OPTION route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function options(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["OPTIONS"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set HEAD route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function head(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["HEAD"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set TRACE route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function trace(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["TRACE"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Set CONNECT route
     *
     * @param string $uri
     * @param string|Process $className
     * @param string|array $method
     * @param array  $middlewares
     *
     * @return void
     */
    public function connect(
        string $uri,
        string|Process $className,
        string|array $method,
        array $middlewares = []
    ) : void {
        array_push(
            $this->routes["CONNET"],
            [
                "uri" => $uri,
                "className" => $this->isProcess($className) ? "process" : $this->containers($className),
                "method" => $method,
                "middlewares" => $middlewares
            ]
        );
    }

    /**
     * Check is a process
     *
     * @param mixed $process
     * @return boolean
     */
    public static function isProcess(mixed $process) : bool {
        return $process == Process::class;
    }
}
