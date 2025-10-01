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
        get => $this->app->config->routes_prefix_length;
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
     * @param string $language The language code
     * @param string $method The method
     * @return string The hash
     */
    protected function getHash(string $route, string $language, string $method) : string
    {
        return hash('sha256', $route) . '-' . md5($method . $language);
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
        $methods = [];
        if (!$method) {
            return static::ALLOWED_METHODS;
        }

        $methods = $this->app->array->get($method);

        $methods = array_map('strtolower', $methods);

        return $methods;
    }

    /**
     * Returns the languages to use for a route
     * @param string|array|null $languages The languages to use
     * @return array The list of languages
     */
    protected function getLanguages(string|array|null $languages = null) : array
    {
        if ($languages === null) {
            return [$this->app->lang->default_code];
        } elseif (is_array($languages)) {
            return $languages;
        }

        if ($languages == '*') {
            return $this->app->lang->codes;
        }

        return [$languages];
    }
}
