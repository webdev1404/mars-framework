<?php
/**
* The Routes Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\Cache\Cacheable\CacheableInterface;

/**
 * The Routes Cache Class
 * Class which handles the caching of routes
 */
class Routes extends Data
{
    /**
     * @var string $driver_name The used driver
     */
    protected string $driver_name {
        get => $this->app->config->cache->routes->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @var array $driver_params The parameters to pass to the driver constructor
     */
    protected array $driver_params = [false, 'cacheable_routes'];

    /**
     * @var string $dir The dir where the routes will be cached
     */
    protected string $dir = 'routes';

    /**
     * Returns the list of hashes which are cached for the prefix of a given route
     * @param string $method The request method
     * @param string $language The language code
     * @param string $prefix The prefix of the route
     * @return array The list of hashes
     */
    public function getHashes(string $method, string $language, string $prefix) : array
    {
        if (!$this->exists('routes-cached') || $this->app->development || $this->app->config->development->routes) {
            $this->cache();
        }

        return $this->get($this->getHashesFilename($method, $language, $prefix)) ?? [];
    }

    /**
     * Caches the routes
     */
    protected function cache()
    {
        //get and store each file contains routes
        $routes = $this->app->router->loader->load();

        foreach ($routes->hashes as $method => $languages_list) {
            foreach ($languages_list as $language => $prefixes_list) {
                foreach ($prefixes_list as $prefix => $hashes_list) {
                    $this->set($this->getHashesFilename($method, $language, $prefix), $this->getHashesData($hashes_list, $routes->data));
                }
            }
        }

        //store the routes names
        foreach ($routes->names as $language => $files_list) {
            foreach ($files_list as $file => $routes_list) {
                $this->set($this->getNamesFilename($file, $language), $routes_list);
            }
        }

        $this->create('routes-cached');

        //delete the routes data, we don't need it anymore
        $routes->reset();
    }

    /**
     * Returns the filename for the hashes cache file
     * @param string $method The request method
     * @param string $language The language code
     * @param string $prefix The route prefix
     * @return string The filename for the hashes cache file
     */
    protected function getHashesFilename(string $method, string $language, string $prefix) : string
    {
        return 'hashes-' . $method . '-' . $language . '-' . $prefix;
    }

    /**
     * Returns the data to be stored in a route cache file
     * @param array $hashes_list The list of route hashes
     * @param array $routes_list The list of route data
     * @return array The data to be stored in a route cache file
     */
    protected function getHashesData(array $hashes_list, array $routes_list) : array
    {
        $routes = new \Mars\Router\Loaders\Routes;
        foreach ($hashes_list as $hash => $key) {
            $routes->hashes[$hash] = $routes->getKey($routes_list[$key]);
        }

        return ['hashes' => $routes->hashes, 'data' => $routes->data];
    }

    /**
     * Returns the filename for the names cache file
     * @param string $file The file name
     * @param string $language The language code
     * @return string The filename for the names cache file
     */
    protected function getNamesFilename(string $file, string $language) : string
    {
        return 'names-' . $file . '-' . $language;
    }

    /**
     * Returns the list of route names
     * @param string $file The file name
     * @param string $language The language code
     * @return array The list of route names
     */
    public function getNames(string $file, string $language) : array
    {
        return $this->get($this->getNamesFilename($file, $language), false) ?? [];
    }
}
