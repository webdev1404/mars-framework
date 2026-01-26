<?php
/**
 * The Module Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

/**
 * The Module Route Handler Class
 * Handles module routes
 */
class Module extends Handler
{
    /**
     * @see Handler::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        $data['params']['action'] = $data['action'];

        return $this->app->modules->get($data['name'], $data['params']);
    }
}
