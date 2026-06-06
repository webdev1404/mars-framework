<?php
/**
 * The Base Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finders;

use Mars\App\Kernel;

/**
 * The Base Route Finder Class
 * Base class for all route finders
 */
abstract class Finder
{
    use Kernel;

    /**
     * Returns the route handler
     * @param string $hash The route hash
     * @param array $data The route data
     */
    abstract public function getRoute(string $hash, array $data);
}
