<?php
/**
 * The Page Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

use Mars\App\Kernel;

/**
 * The Page Route Handler Class
 * Handles page routes
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
