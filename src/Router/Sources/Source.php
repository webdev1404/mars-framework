<?php
/**
* The Routes Source Base Class
* @package Mars
*/

namespace Mars\Router\Sources;

use Mars\Router\Base;
use Mars\Router\Routes;

/**
 * The Routes Source Base Class
 * Base class for route sources
 */
abstract class Source extends Base
{
    /**
     * @var Routes $routes The routes object
     */
    public Routes $routes;

    /**
     * Loads the routes from the source
     */
    abstract public function load();
}
