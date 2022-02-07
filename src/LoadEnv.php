<?php
/**
 * LoadEnv
 *
 * PHP version 8
 *
 * @category LoadEnv
 * @package  LoadEnv
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

namespace Gino;

/**
 * LoadEnv class
 *
 * @category LoadEnv
 * @package  LoadEnv
 * @author   Riccardo Curcio <curcioriccardo@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://url.com
 */

class LoadEnv{
  public static function load($path)
    {
      $fn = fopen($path."/.env","r");

      while(! feof($fn))  {
        $result = trim(fgets($fn));
        if ( strlen($result) > 0 && $result[0] != '#'){
          putenv($result);
        }
      }

      fclose($fn);
    }
}
