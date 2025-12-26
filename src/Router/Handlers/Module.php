<?php
/**
 * The Module Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

/**
 * The Page Route Handler Class
 * Handles page routes
 */
class Module extends Handler
{
    /**
     * @see Handler::getRoute()
     * {@inheritdoc}
     */
    public function getRoute(string $hash, array $data)
    {
        $data['params']['action'] = $data['action'];

        return new \Mars\Extensions\Modules\Module($data['name'], $data['params'], $this->app);
    }
}
