<?php
/**
 * The Action Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

use Mars\Router\Loaders\Files;
use Mars\Router\Loaders\Routes;

/**
 * The Action Route Handler Class
 * Handles action routes
 */
class Action extends Files
{
    /**
     * @var array $hashes The route hashes to load
     */
    protected array $hashes = [];

    /**
     * @see Handler::getRoute()
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
     * @see \Mars\Router\Loaders\Loader::loadHash()
     * {@inheritDoc}
     */
    protected function loadHash(string $method, string $language, string $route, string $prefix, string $hash, string $type, array $data, null|string|callable|array $action)
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
     * @see \Mars\Router\Loaders\Loader::loadName()
     * {@inheritDoc}
     */
    protected function loadName(string $language, string $name, string $route)
    {
        //no need to load names for action routes
        return;
    }
}
