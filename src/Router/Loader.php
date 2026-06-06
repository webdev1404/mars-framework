<?php
/**
* The Loader Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Handlers;
use Mars\Router\Loaders\Routes;

/**
 * The Loader Class
 * Loads the routes from defined loaders
 */
class Loader extends Base
{
    /**
     * @var array $supported_loaders The list of supported route loaders
     */
    public protected(set) array $supported_loaders = [
        'pages' => \Mars\Router\Loaders\Pages::class,
        'files' => \Mars\Router\Loaders\Files::class,
    ];

    /**
     * @var Handlers $loaders The loaders object
     */
    public protected(set) Handlers $loaders {
        get {
            if (isset($this->loaders)) {
                return $this->loaders;
            }

            $this->loaders = new Handlers($this->supported_loaders, null, $this->app);

            return $this->loaders;
        }
    }

    /**
     * @var Routes $routes The routes data container
     */
    public protected(set) Routes $routes {
        get {
            if (isset($this->routes)) {
                return $this->routes;
            }

            $this->routes = new Routes;

            return $this->routes;
        }
    }

    /**
     * Loads the routes from all the loaders
     * @return Routes The routes data
     */
    public function load() : Routes
    {
        foreach ($this->loaders as $loader) {
            $loader->routes = $this->routes;
            $loader->load();
        }

        return $this->routes;
    }
}
