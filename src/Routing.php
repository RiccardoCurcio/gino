<?php
/**
 * Routing
 *
 * PHP version 8
 *
 * @category Routing
 * @package  Routing
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino;

/**
 * Routing class
 *
 * @category Routing
 * @package  Routing
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
class Routing
{
    protected $routes;

    protected $add;

    use \Gino\Src\Routes;
    use \Gino\Src\Runner;
    use \Gino\Src\CorsOrigin\CorsOrigin;
    use \Gino\Src\CorsHeaders\CorsHeaders;
    
    /**
     * Routing constructor
     *
     * @param array|null $add
     *
     * @return void
     */
    public function __construct(?array $add = [])
    {
        $this->add = $add;
        $this->routes = [
            "GET"       => [],
            "POST"      => [],
            "PUT"       => [],
            "PATCH"     => [],
            "DELETE"    => [],
            "OPTIONS"    => [],
            "HEAD"      => [],
            "TRACE"     => [],
            "CONNECT"   => []
        ];
       
        if (filter_var(getenv("CROSS_ORIGIN_RESOLVE"), FILTER_VALIDATE_BOOLEAN)) {
            $this->resolveCors($this);
        }
    }
}
