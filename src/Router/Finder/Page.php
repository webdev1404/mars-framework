<?php
/**
 * The Page Route Finder Class
 * @package Mars
 */
namespace Mars\Router\Finder;

/**
 * The Page Route Finder Class
 * Handles page routes
 */
class Page extends Finder
{
    /**
     * @see Finder::getRoute()
     * {@inheritDoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Content\Page($data['page'], $this->app);
    }
}
