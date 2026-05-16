<?php
/**
* The Modules Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Modules Cache Class
 * Class which handles the caching of module files
 */
class Modules extends Data
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->modules->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_modules',     // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'modules';
}
