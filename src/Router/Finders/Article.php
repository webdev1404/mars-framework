<?php
/**
 * The Article Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finders;

/**
 * The Article Route Finder Class
 * Handles article routes
 */
class Article extends Finder
{
    /**
     * @see Finder::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Content\Article($data, $this->app);
    }
}
