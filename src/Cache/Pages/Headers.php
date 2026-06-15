<?php
/**
* The Page Headers Cache Class
* @package Mars
*/

namespace Mars\Cache\Pages;

use Mars\Cache\Cacheable;

/**
 * The Page Headers Cache Class
 * Class which handles the caching of page headers
 */
class Headers extends Cacheable
{
    /**
     * @see Cacheable::$drivers_enabled
     * {@inheritDoc}
     */
    public protected(set) array $drivers_enabled = ['serialized', 'memcache'];

    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     * It will use memcache if the page cache driver is memcache, otherwise it will use serialized
     */
    public string $driver_name {
        get {
            if (isset($this->driver_name)) {
                return $this->driver_name;
            }

            $this->driver_name = $this->app->cache->pages->driver_name == 'memcache' ? 'memcache' : 'serialized';

            return $this->driver_name;
        }
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,                       // use files cache
        'cacheable_pages_headers',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'pages';

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;

    /**
     * @see Cacheable::$extension
     * {@inheritDoc}
     */
    public protected(set) string $extension = 'head';
}
