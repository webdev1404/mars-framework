<?php
/**
 * The Base Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

use Mars\App\Kernel;

/**
 * The Base Route Handler Class
 * Base class for all route handlers
 */
abstract class Handler
{
    use Kernel;

    /**
     * Returns the route handler
     * @param string $hash The route hash
     * @param array $data The route data
     */
    abstract public function getRoute(string $hash, array $data);
}
