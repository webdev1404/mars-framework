<?php
/**
* The Assets List Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Lists;

use Mars\Cache\Cacheable;

/**
 * The Assets List Cache Class
 * Class which handles the caching of asset lists
 */
abstract class Assets extends Cacheable
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->assets->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,               // use files cache
        'cacheable_assets',   // driver type
    ];

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;
}
