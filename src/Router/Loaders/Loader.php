<?php
/**
* The Routes Loader Base Class
* @package Mars
*/

namespace Mars\Router\Loaders;

use Mars\Router\Base;

/**
 * The Routes Loader Base Class
 * Base class for route loaders
 */
abstract class Loader extends Base
{
    /**
     * @var Routes $routes The routes object
     */
    public Routes $routes;

    /**
     * Loads the routes
     */
    abstract public function load();

    /**
     * Loads a route's name
     * @param string $language The language code
     * @param string $name The name of the route
     * @param string $route The route
     */
    protected function loadName(string $language, string $name, string $route)
    {
        $file = $this->getNameFile($name);

        $this->routes->names[$language][$file][$name] = $route;
    }

    /**
     * Loads a hash
     * @param string $method The request method
     * @param string $language The language code
     * @param string $route The route
     * @param string $prefix The route's prefix
     * @param string $hash The hash to append
     * @param string $type The route type
     * @param string|null $name The route name
     * @param bool $sitemap Whether to include the route in the sitemap
     * @param array $data Route's data
     * @param string|callable|array $action The route action
     */
    protected function loadHash(string $method, string $language, string $route, string $prefix, string $hash, string $type, ?string $name, bool $sitemap, array $data, null|string|callable|array $action)
    {
        $this->routes->hashes[$method][$language][$prefix][$hash] = $this->routes->getKey($this->getData($route, $type, $language, $name, $sitemap, $data));
    }

    /**
     * Unloads a hash
     * @param string $method The request method
     * @param string $language The language code
     * @param string $prefix The route's prefix
     * @param string $hash The hash to unload
     */
    protected function unloadHash(string $method, string $language, string $prefix, string $hash)
    {
        unset($this->routes->hashes[$method][$language][$prefix][$hash]);
    }

    /**
     * Returns the data for a route
     * @param string $route The route
     * @param string $type The route type
     * @param string|null $name The route name
     * @param array $data Route's data
     * @return array The route data
     */
    protected function getData(string $route, string $type, string $language, ?string $name, bool $sitemap, array $data) : array
    {
        return ['route' => $route, 'type' => $type, 'language' => $language, 'name' => $name, 'sitemap' => $sitemap, 'preg' => $this->getContainsPreg($route), 'data' => $data];
    }
}
