<?php
/**
* The Cacheable Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App\Drivers;
use Mars\Cache\Cacheable\CacheableInterface;

/**
 * The Cacheable Class
 * Caches content & serves it from cache
 */
abstract class Cacheable extends Cache
{
    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'file' => \Mars\Cache\Cacheable\File::class,
        'php' => \Mars\Cache\Cacheable\Php::class,
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

            $this->drivers = new Drivers($this->supported_drivers, CacheableInterface::class, 'cachable', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var CacheableInterface $driver The driver object
     */
    public protected(set) CacheableInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->driver_name, ...$this->driver_params);
            return $this->driver;
        }
    }

    /**
     * @var array $driver_params The parameters to pass to the driver constructor
     */
    protected array $driver_params = [true, 'cacheable'];

    /**
     * @var string $driver The used driver
     */
    protected string $driver_name {
        get => $this->app->config->cache->driver;
    }

    /**
     * @var bool $can_hash Whether to hash the filename or not
     */
    protected bool $can_hash = false;

    /**
     * @var string $hash The hash algo. used to generate the cache file name
     */
    protected string $hash {
        get => $this->app->config->cache->hash;
    }

    /**
     * @var bool $serialize Whether to serialize the data before storing
     */
    protected bool $serialize = true;

    /**
     * Gets a cached value
     * @param string $name The name of the cached data
     * @return mixed The cached value
     */
    public function get(string $name) : mixed
    {
        return $this->driver->get($this->getFilename($name), $this->serialize);
    }

    /**
     * Sets The value of a cached value
     * @param string $name The name
     * @param mixed $value The value
     * @return static $this
     */
    public function set(string $name, mixed $value) : static
    {
        $this->driver->set($this->getFilename($name), $value, $this->serialize);

        return $this;
    }

    /**
     * Creates a cached value
     * @param string $name The name of the cached value
     * @return static $this
     */
    public function create(string $name) : static
    {
        $this->driver->create($this->getFilename($name));

        return $this;
    }

    /**
     * Checks if a cached value exists
     * @param string $name The name of the cached value
     * @return bool True if the cached value exists, false otherwise
     */
    public function exists(string $name) : bool
    {
        return $this->driver->exists($this->getFilename($name));
    }

    /**
     * Deletes a cached value
     * @param string $name The name of the value to unset
     * @return static $this
     */
    public function delete(string $name) : static
    {
        $filename = $this->getFilename($name);

        $this->driver->delete($filename);

        return $this;
    }

    /**
     * Cleans the cache
     */
    public function clean() : static
    {
        $this->driver->clean($this->path);

        return $this;
    }

    /**
     * Gets the filename for a cache file
     * @param string $filename The name of the file
     * @return string The filename
     */
    public function getFilename(string $filename) : string
    {
        return $this->path . '/' . $this->getName($filename);
    }

    /**
     * Returns the file name where the content will be cached
     * @param string $name The name of the filee
     * @return string
     */
    protected function getName(string $name) : string
    {
        if (!$this->can_hash) {
            return $name;
        }

        return hash($this->hash, $name);
    }
}
