<?php
/**
* The Cacheable Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Drivers;
use Mars\Cache\Cacheable\DriverInterface;

/**
* The Cacheable Class
 * Caches content & serves it from cache
 */
abstract class Cacheable
{
    use InstanceTrait;
    
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
    protected string $driver_name = 'file'; 

    /**
     * @var string $path The folder where the content will be cached
     */
    protected string $path = '';

    /**
     * @var string $file The name of the file used to cache the content
     */
    protected string $file = '';

    /**
     * @var string $filename The filename of the file used to cache the content
     */
    protected string $filename {
        get {
            if (isset($this->filename)) {
                return $this->filename;
            }

            $this->filename = $this->path . '/' . $this->getFile($this->file);

            return $this->filename;
        }
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
     * Returns the file where the content will be cached
     * @param string $id The id of the page
     * @return string
     */
    protected function getFile(string $id) : string
    {
        return hash($this->hash, $id . $this->app->config->key) . '.' . $this->extension;
    }
}
