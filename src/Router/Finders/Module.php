<?php
/**
 * The Module Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finders;

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
        $params = $data['params'] ?? [];
        $params['action'] = $data['action'];

        return $this->app->modules->get($data['name'], $params);
    }
}
