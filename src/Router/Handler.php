<?php
/**
* The Routes Handler Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Mvc\Controller;

/**
 * The Routes Handler Class
 * Handles the route retrieval
 */
class Handler
{
    use Kernel;
    
    /**
     * @var array $supported_handlers The supported route handlers
     */
    public protected(set) array $supported_handlers = [
        'action' => \Mars\Router\Handlers\Action::class,
        'module' => \Mars\Router\Handlers\Module::class,
        'page' => \Mars\Router\Handlers\Page::class,
        'template' => \Mars\Router\Handlers\Template::class,
    ];

    /**
     * @var Handlers $handlers The handlers object
     */
    public protected(set) Handlers $handlers {
        get {
            if (isset($this->handlers)) {
                return $this->handlers;
            }

            $this->handlers = new Handlers($this->supported_handlers, null, $this->app);

            return $this->handlers;
        }
    }

    /**
     * Returns the route by its hash or preg match
     * @param string $hash The hash of the route
     * @param array $hashes The route hashes
     * @return array|null The route, or null if not found
     */
    public function get(string $hash, array $hashes) : array|null
    {
        if (isset($hashes['hashes'][$hash])) {
            return $this->getByHash($hash, $hashes);
        }

        return $this->getByPreg($hashes);
    }

    /**
     * Returns the route by its hash
     * @param string $hash The hash of the route
     * @param array $hashes The route hashes
     * @return array|null The route, or null if not found
     */
    public function getByHash(string $hash, array $hashes) : array|null
    {
        $key = $hashes['hashes'][$hash] ?? null;
        if ($key === null) {
            return null;
        }

        $route = $hashes['data'][$key] ?? null;
        if ($route === null) {
            return null;
        }

        return $this->getRoute($hash, $route);
    }

    /**
     * Returns the route
     * @param string $hash The hash of the route
     * @param array $route The route data
     * @param array $params The route parameters
     * @return array|null The route, or null if not found
     */
    protected function getRoute(string $hash, array $route, array $params = []) : ?array
    {
        $handler = $this->handlers->get($route['type']);
        if (!$handler) {
            return null;
        }
        
        return [$handler->getRoute($hash, $route['data']), $params];
    }

    /**
     * Returns the route from a preg match
     * @param array $hashes The list of hashes
     * @return array|null The route, or null if not found
     */
    public function getByPreg(array $hashes) : array|null
    {
        $possible_routes = array_filter($hashes['data'], fn ($route) => $route['preg'] ?? false);
        if (!$possible_routes) {
            return null;
        }

        $possible_hashes = array_filter($hashes['hashes'], fn ($hash_key) => isset($possible_routes[$hash_key]));
        if (!$possible_hashes) {
            return null;
        }

        foreach ($possible_hashes as $hash => $key) {
            $route = $hashes['data'][$key];
            
            $route_path = preg_replace_callback('/{([a-z0-9_]*)}/is', function ($match) {
                return '(.*)';
            }, $route['route']);
            
            if (preg_match("|^{$route_path}$|is", $this->app->router->route, $matches)) {
                $params = array_slice($matches, 1);

                return $this->getRoute($hash, $route, $params);
            }
        }

        return null;
    }
}
