<?php
/**
* The Data Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Data Cache Class
 * Class which handles the caching of data
 */
class Data extends Cacheable
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->data->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,               // use files cache
        'cacheable_data',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'data';
}
