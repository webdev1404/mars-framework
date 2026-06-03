<?php
/**
 * The Callable Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finder;

use Mars\Router\Loader\Files;
use Mars\Router\Loader\Routes;

/**
 * The Callable Route Finder Class
 * Handles callable routes
 */
class Callable_ extends Files
{
    /**
     * @var array $hashes The route hashes to load
     */
    protected array $hashes = [];

    /**
     * @see Finder::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        $this->hashes = [$hash => true];
        $this->routes = new Routes;

        $this->loadFile($data['filename']);

        return $this->routes->hashes[$hash] ?? null;
    }

    /**
     * @see \Mars\Router\Loader\Loader::loadHash()
     * {@inheritDoc}
     */
    protected function loadHash(string $method, string $language, string $route, string $prefix, string $hash, string $type, string $name, array $data, null|string|callable|array $action)
    {
        if ($method != $this->app->request->method) {
            return;
        }
        if ($language != $this->app->lang->code) {
            return;
        }
        if ($this->hashes && !isset($this->hashes[$hash])) {
            return;
        }

        $this->routes->hashes[$hash] = $action;
    }

    /**
     * @see \Mars\Router\Loader\Loader::loadName()
     * {@inheritDoc}
     */
    protected function loadName(string $language, string $name, string $route)
    {
        //no need to load names for action routes
        return;
    }
}
