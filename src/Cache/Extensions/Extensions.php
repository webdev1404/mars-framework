<?php
/**
* The Extensions Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

use Mars\Cache\Data;

/**
 * The Extensions Cache Class
 * Class which handles the caching of extension files
 */
class Extensions extends Data
{
    /**
     * @see \Mars\Cache\Cacheable::$driver_name
     * {@inheritDoc}
     */
    public string $driver_name {
        get => $this->app->config->cache->extensions->driver ?? $this->app->config->cache->driver;
    }
}
