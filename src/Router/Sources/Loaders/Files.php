<?php
/**
* The Files Routes Loader Class
* @package Mars
*/

namespace Mars\Router\Sources\Loaders;

use Mars\Mvc\Controller;
use Mars\Router\Routes;

/**
 * The Files Routes Loader Class
 * Loads routes from files
 */
class Files extends \Mars\Router\Sources\Files
{
    /**
     * @var array $hashes The list of hashes to load
     */
    protected array $hashes = [];

    /**
     * Returns the route by its hash
     * @param string $hash The hash of the route
     * @param array $data The route data
     * @return array|null The route, or null if not found
     */
    public function getByHash(string $hash, array $data) : array|null
    {
        $this->hashes = array_fill_keys([$hash], true);

        $this->loadFile($data['filename']);
        
        //was the route found?
        if (!$this->routes->list && !isset($this->routes->list[$hash])) {
            return null;
        }

        return [$this->routes->list[$hash]['action'], []];
    }

    /**
     * Returns the route from a preg match
     * @param array $hashes The list of hashes
     * @return array|null The route, or null if not found
     */
    public function getByPreg(array $hashes) : array|null
    {
        $hashes = array_filter($hashes, fn ($route) => $route['preg']);
        if (!$hashes) {
            return null;
        }

        $this->hashes = array_fill_keys(array_keys($hashes), true);

        //search all the files that might contain the preg route
        $filenames = array_unique(array_column($hashes, 'filename'));
        foreach ($filenames as $filename) {
            $this->routes->reset();

            $this->loadFile($filename);

            //search for the matching preg
            foreach ($this->routes->list as $hash => $route) {
                $route_path = preg_replace_callback('/{([a-z0-9_]*)}/is', function ($match) {
                    return '(.*)';
                }, $route['route']);

                if (preg_match("|^{$route_path}$|is", $this->app->router->path, $matches)) {
                    $params = array_slice($matches, 1);

                    return [$route['action'], $params];
                }
            }
        }

        return null;
    }

    /**
     * @see \Mars\Router\Sources\Files::canLoadHash()
     * {@inheritdoc}
     */
    protected function canLoadHash(string $hash) : bool
    {
        if ($this->hashes && !isset($this->hashes[$hash])) {
            return false;
        }

        return true;
    }

    /**
     * @see \Mars\Router\Sources\Files::getData()
     * {@inheritdoc}
     */
    protected function getData(string $route, string|callable|Controller $action) : array
    {
        $data = parent::getData($route, $action);
        
        $data['action'] = $action;

        return $data;
    }
}
