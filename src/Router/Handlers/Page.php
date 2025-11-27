<?php
/**
 * The Page Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

/**
 * The Page Route Handler Class
 * Handles page routes
 */
class Page extends Handler
{
    /**
     * @see Handler::getRoute()
     * {@inheritdoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Content\Page($data['page'], $this->app);
    }
}
