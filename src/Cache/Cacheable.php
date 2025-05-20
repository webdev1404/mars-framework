<?php
/**
* The Cacheable Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Drivers;
use Mars\Cache\Cacheable\DriverInterface;

/**
 * The Cacheable Class
 * Caches content & serves it from cache
 */
abstract class Cacheable extends Base
{
    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'file' => \Mars\Cache\Cacheable\File::class,
        'memcache' => \Mars\Cache\Cacheable\Memcache::class,
    ];    

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'cachable', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    public protected(set) DriverInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->driver_name);

            return $this->driver;
        }
    }

    /**
     * @var string $driver The used driver
     */
    protected string $driver_name {
        get => $this->app->config->cache_driver;
    }

    /**
     * @var string $extension The extension of the cache file
     */
    protected string $extension = '';

    /**
     * @var string $hash The hash algo. used to generate the cache file name
     */
    protected string $hash {
        get => $this->app->config->cache_hash;
    }

    /**
     * Returns the file name where the content will be cached
     * @param string $id The id of the page
     * @param string|null $extension The extension of the file. If null, $this->extension will be used
     * @return string
     */
    protected function getName(string $id, ?string $extension = null) : string
    {
        if (!$extension) {
            $extension = $this->extension;
        }

        return hash($this->hash, $id . $this->app->config->key) . '.' . $extension;
    }
}
