<?php
/**
 * The Module Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finder;

/**
 * The Module Route Finder Class
 * Handles module routes
 */
class Module extends Finder
{
    /**
     * @see Finder::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        $data['params']['action'] = $data['action'];

        return $this->app->modules->get($data['name'], $data['params']);
    }
}
