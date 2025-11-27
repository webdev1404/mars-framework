<?php
/**
 * The Template Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

/**
 * The Template Route Handler Class
 * Handles template routes
 */
class Template extends Handler
{
    /**
     * @see Handler::getRoute()
     * {@inheritdoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Content\Template($data['template'], $this->app);
    }
}
