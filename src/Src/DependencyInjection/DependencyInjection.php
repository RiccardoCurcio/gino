<?php
/**
 * DependencyInjection
 *
 * PHP version 8
 *
 * @category DependencyInjection
 * @package  DependencyInjection
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino\Src\DependencyInjection;

/**
 * DependencyInjection trait
 *
 * @category DependencyInjection
 * @package  DependencyInjection
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */
trait DependencyInjection
{
    /**
     * Resolve dependenciese (e' inutile che fai lo splendido tanto ti devo riscrivere!)
     *
     * @param string $className
     * @return object
     */
    public function containers(string $className) : object
    {
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();
        
        if ($reflection->isInstantiable()) {
            $dependenciesArray = [];
            foreach ($constructor->getParameters() as $value) {
                $dependency = $this->getHint(
                    $constructor->getDocComment(),
                    $value->name
                );
                
                if ($dependency) {
                    $reflectionDep = new \ReflectionClass($dependency);
                    
                    if ($reflectionDep->isInstantiable()) {
                        $constructorDep = $reflectionDep->getConstructor();
                        if (count($constructorDep->getParameters()) > 0) {
                            array_push(
                                $dependenciesArray,
                                $this->containers($dependency)
                            );
                        } else {
                            array_push(
                                $dependenciesArray,
                                $reflectionDep->newInstanceArgs()
                            );
                        }
                    }
                }
            }
            return $reflection->newInstanceArgs($dependenciesArray);
        }
    }

    /**
     * Dependecies type from docComment
     *
     * @param string $docComment
     * @param string $varName
     *
     * @return string|null
     */
    public static function getHint(string $docComment, string $varName) : string|null
    {
        $matches = array();
        $count = preg_match_all(
            '/@dependency[\t\s]*(?P<type>[^\t\s]*)[\t\s]*\$(?P<name>[^\t\s]*)/sim',
            $docComment,
            $matches
        );

        if ($count > 0) {
            foreach ($matches['name'] as $n => $name) {
                if ($name == $varName) {
                    return $matches['type'][$n];
                }
            }
        }

        return null;
    }
}
