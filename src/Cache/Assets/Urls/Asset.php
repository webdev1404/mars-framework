<?php
/**
* The Assets Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Urls;

use Mars\Cache\Cacheable;

/**
 * The Assets Cache Class
 * Base class which handles the caching of asset files
 */
abstract class Asset extends Cacheable
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => 'text';
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,                // use files cache
        'cacheable_assets',   // driver type
    ];

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;

    /**
     * @see Cacheable::$serialize
     * {@inheritDoc}
     */
    protected bool $serialize = false;

    /**
     * @var string $base_url The base url for the cached assets
     */
    public protected(set) string $base_url {
        get {
            if (isset($this->base_url)) {
                return $this->base_url;
            }
            
            $this->base_url = $this->app->assets_url . '/cache/' . $this->dir;

            return $this->base_url;
        }
    }
}
