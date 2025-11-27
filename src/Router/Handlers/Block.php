<?php
/**
 * The Block Route Handler Class
 * @package Mars
 */
namespace Mars\Router\Handlers;

/**
 * The Page Route Handler Class
 * Handles page routes
 */
class Block extends Handler
{
    /**
     * @see Handler::getRoute()
     * {@inheritdoc}
     */
    public function getRoute(string $hash, array $data)
    {
        return new \Mars\Extensions\Modules\Block($data['module_name'], $data['block_name'], $data['params'], $this->app);
    }
}
