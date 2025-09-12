<?php
/**
* The Routes Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Extensions\Module;
use Mars\Router\Routes as RouterRoutes;

/**
 * The Routes Cache Class
 * Class which handles the caching of routes
 */
class Routes extends Data
{
    /**
     * @var string $dir The dir where the routes will be cached
     */
    protected string $dir = 'routes';

    /**
     * @var int $prefix_length The length of the prefix used for route caching
     */
    protected int $prefix_length {
        get => $this->app->config->cache_routes_prefix_length;
    }

    /**
     * Returns the list of hashes which are cached for the prefix of a given route
     * @param string $route The route
     * @return array The list of hashes
     */
    public function getHashes(string $route) : array
    {
        $prefix = $this->getPrefix($route);

        $files_list = $this->getArray('files-list', false);

        if ($this->app->development || $this->app->config->development_routes) {
            $files_list = null;
        }

        if ($files_list === null) {
            $files_list = $this->cache();
        }

        //no such file, thus route. No need to look further
        if (!isset($files_list[$prefix])) {
            return [];
        }

        return $this->getArray('hashes-' . $prefix, false);
    }

    /**
     * Caches the routes
     * @return array The list of cached route files
     */
    protected function cache() : array
    {
        $hashes = [];
        $routes = new RouterRoutes($this->app);

        $files_list = $this->getFilesList();
        foreach ($files_list as $key => $file) {
            $routes->load($file);
        }

        $files_list = [];
        foreach ($routes->routes_list as $hash => $route) {
            $prefix = $this->getPrefix($route['route']);
            
            if (!isset($files_list[$prefix])) {
                $files_list[$prefix] = [];
            }

            $files_list[$prefix][$hash] = ['filename' => $route['filename'], 'preg' => $this->getContainsPreg($route['route'])];
        }

        //store each file contains routes
        foreach ($files_list as $prefix => $hashes_list) {
            $this->setArray('hashes-' . $prefix, $hashes_list, false);
        }

        //store the file containing the list of route files
        $files_list = array_fill_keys(array_keys($files_list), true);
        $this->setArray('files-list', $files_list, false);

        return $files_list;
    }

    /**
     * Returns the list of files containing routes
     * @return array The list of route files
     */
    protected function getFilesList() : array
    {
        $files_list = [];

        //get the routes files from the modules dirs
        $modules = Module::getList();
        foreach ($modules as $module_path) {
            $module_files_list = $this->getFilesFromDir($module_path . '/' . Module::DIRS['routes']);
            $files_list = array_merge($files_list, $module_files_list);
        }

        //get the routes files from the app dir
        $app_routes = $this->getFilesFromDir($this->app->app_path . '/routes');
        $files_list = array_merge($files_list, $app_routes);

        return $files_list;
    }

    /**
     * Returns the list of cached route files
     * @param string $dir The dir where to look for cached route files
     * @return array The list of cached route files
     */
    protected function getFilesFromDir(string $dir) : array
    {
        if (!is_dir($dir)) {
            return [];
        }

        return $this->app->dir->getFilesSorted($dir, true, true, extensions: ['php']);
    }

    /**
     * Returns the prefix for a given route
     * @param string $route The route
     * @return string The prefix
     */
    protected function getPrefix(string $route) : string
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
     * Determines if a route contains preg patterns
     * @param string $route The route
     * @return bool True if the route contains preg patterns, false otherwise
     */
    protected function getContainsPreg(string $route) : bool
    {
        return preg_match('/\{([a-z0-9_]+)\}/isU', $route);
    }
}
