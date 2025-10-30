<?php
/**
* The Routes Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Router\Routes as RoutesList;

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
     * Returns the list of hashes which are cached for the prefix of a given route
     * @param string $prefix The prefix of the route
     * @return array The list of hashes
     */
    public function getHashes(string $prefix) : array
    {
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

        return $this->getArray('hashes-' . $prefix, false) ?? [];
    }

    /**
     * Caches the routes
     * @return array The list of cached route files
     */
    protected function cache() : array
    {
        //get and store each file contains routes
        $list = $this->app->router->routes->load();
        $this->app->router->routes->reset();

        $files_list = $this->getFilesList($list);
        foreach ($files_list as $prefix => $hashes_list) {
            $this->setArray('hashes-' . $prefix, $hashes_list, false);
        }

        //store the file containing the list of route files
        $files_list = array_fill_keys(array_keys($files_list), true);
        $this->setArray('files-list', $files_list, false);

        return $files_list;
    }

    /**
     * Splits and returns the list of cached route files
     * @param array $list The list routes
     * @return array The list of cached route files
     */
    protected function getFilesList(array $list) : array
    {
        $files_list = [];
        foreach ($list as $hash => $data) {
            $prefix = $data['prefix'];
            if (!isset($files_list[$prefix])) {
                $files_list[$prefix] = [];
            }

            $files_list[$prefix][$hash] = $data;
        }

        return $files_list;
    }
}
