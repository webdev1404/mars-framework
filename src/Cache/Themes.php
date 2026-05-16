<?php
/**
* The Themes Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Themes Cache Class
 * Class which handles the caching of theme files
 */
class Themes extends Data
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->themes->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_themes',      // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'themes';
}
