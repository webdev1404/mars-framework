<?php
/**
* The Routes Finder Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Mvc\Controller;

/**
 * The Routes Finder Class
 * Handles the route retrieval
 */
class Finder
{
    use Kernel;
    
    /**
     * @var array $supported_finders The supported route finder handlers
     */
    public protected(set) array $supported_finders = [
        'callable' => \Mars\Router\Finders\Callable_::class,
        'module' => \Mars\Router\Finders\Module::class,
        'page' => \Mars\Router\Finders\Page::class,
        'template' => \Mars\Router\Finders\Template::class,
    ];

    /**
     * @var Handlers $finders The finders object
     */
    public protected(set) Handlers $finders {
        get {
            if (isset($this->finders)) {
                return $this->finders;
            }

            $this->finders = new Handlers($this->supported_finders, null, $this->app);

            return $this->finders;
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
        $finder = $this->finders->get($route['type']);
        if (!$finder) {
            return null;
        }

        return [$finder->getRoute($hash, $route['data']), $params, ['pattern' => $route['route'], 'name' => $route['name']]];
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
