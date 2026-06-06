<?php
/**
 * The Template Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finders;

/**
 * The Template Route Finder Class
 * Handles template routes
 */
class Template extends Finder
{
    /**
     * @see Finder::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Content\Template($data['template'], $this->app);
    }
}
