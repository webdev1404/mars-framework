<?php
/**
* The Base Router Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Kernel;
use Closure;

/**
 * The Base Router Class
 * Base class for route handling
 */
abstract class Base
{
    use Kernel;

    /**
     * @const array ALLOWED_METHODS The allowed request methods
     */
    public const array ALLOWED_METHODS = ['get', 'post', 'put', 'delete'];

    /**
     * @var int $prefix_length The length of the prefix used for route caching
     */
    protected int $prefix_length {
        get => $this->app->config->routes->prefix_length;
    }

    /**
     * Returns the prefix for a given route
     * @param string $route The route
     * @return string The prefix
     */
    public function getPrefix(string $route) : string
    {
        if ($route === '' || $route === '/') {
            return 'root';
        }

        $prefix = substr($route, 0, $this->prefix_length);

        //make sure the prefix does not cut a folder name
        $slash_pos = strrpos($prefix, '/');
        if ($slash_pos === false) {
            return $prefix;
        }

        return substr($prefix, 0, $slash_pos);
    }

    /**
     * Returns the hash for a route
     * @param string $route The route
     * @return string The hash
     */
    protected function getHash(string $route) : string
    {
        return hash('sha256', $route);
    }

    /**
     * Determines if a route contains preg patterns
     * @param string $route The route
     * @return bool True if the route contains preg patterns, false otherwise
     */
    protected function getContainsPreg(string $route) : bool
    {
        return preg_match('/\{([a-z0-9_]+)\}/isU', $route);
    }

    /**
     * Cleans the route name
     * @param string $route The route to clean
     * @return string The cleaned route name
     */
    protected function getName(string $route) : string
    {
        //strip the leading slash, for all the routes except the root
        if ($route != '/') {
            $route = ltrim($route, '/');
        }

        return $route;
    }

    /**
     * Returns the methods to use for a route
     * @param string|array $method The method or methods to use
     * @return array The list of methods
     */
    protected function getMethods(string|array $method) : array
    {
        if (!$method) {
            return static::ALLOWED_METHODS;
        }

        return array_map('strtolower', (array)$method);
    }

    /**
     * Returns the file name for a route's name
     * @param string $name The name of the route
     * @return string The file name
     */
    public function getNameFile(string $name) : string
    {
        $dot_pos = strpos($name, '.');
        if ($dot_pos === false) {
            return 'global';
        }

        return substr($name, 0, $dot_pos);
    }
}
