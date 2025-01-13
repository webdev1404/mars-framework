<?php
/**
* The Memcache Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Memcache\DriverInterface;

/**
 * The Memcache Class
 * Handles the interactions with the memory cache.
 * Not the same as the memcache extension, although it might use it as a driver
 */
class Memcache
{
    use InstanceTrait;

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'redis' => \Mars\Memcache\Redis::class,
        'memcache' => \Mars\Memcache\Memcache::class,
        'memcached' => \Mars\Memcache\Memcached::class
    ];
    
    /**
     * @var bool $enabled Will be set to true, if memcache is enabled
     */
    public bool $enabled {
        get => $this->app->config->memcache_enable;
    }

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'memcache', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    public protected(set) ?DriverInterface $driver {
        get {
            if (!$this->enabled) {
                return null;
            }            
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->memcache_driver);
            $this->driver->connect($this->host, $this->port);

            return $this->driver;
        }
    }

    /**
     * @var string $host The host to connect to
     */
    protected string $host {
        get => $this->app->config->memcache_host;
    }

    /**
     * @var string $port The port to connect to
     */
    protected string $port {
        get => $this->app->config->memcache_port;
    }

    /**
     * @var string $key Secret key used to identify the site
     */
    protected string $key {
        get => $this->app->config->key;
    }

    /**
     * Destroys the memcache object. Disconnects from the memcache server
     */
    public function __destruct()
    {
        if (isset($this->driver)) {
            $this->driver->disconnect();
        }
    }

    /**
     * Adds a key to the memcache only if it doesn't already exists
     * @param string $key The key
     * @param string $value The value
     * @param bool $serialize If true, will serialize the value
     * @param int $expires The number of seconds after which the data will expire
     * @return bool
     */
    public function add(string $key, $value, bool $serialize = false, int $expires = 0)
    {
        if (!$this->enabled) {
            return false;
        }

        if ($serialize) {
            $value = $this->app->serializer->serialize($value, false);
        }

        return $this->driver->add($key . '-' . $this->key, $value, $expires);
    }

    /**
     * Adds a key to the memcache. If a key with the same name exists, it's value is overwritten
     * @param string $key The key
     * @param string $value The value
     * @param bool $serialize If true, will serialize the value
     * @param int $expires The number of seconds after which the data will expire
     * @return bool
     */
    public function set(string $key, $value, bool $serialize = false, int $expires = 0) : bool
    {
        if (!$this->enabled) {
            return false;
        }
    
        if ($serialize) {
            $value = $this->app->serializer->serialize($value, false);
        }

        return $this->driver->set($key . '-' . $this->key, $value, $expires);
    }

    /**
     * Retrieves the value of $key from the memcache
     * @param string $key The key
     * @param bool $unserialize If true, will unserialize the returned result
     * @return mixed The value of $key
     */
    public function get(string $key, bool $unserialize = false)
    {
        if (!$this->enabled) {
            return false;
        }

        $value = $this->driver->get($key . '-' . $this->key);

        if ($unserialize) {
            return $this->app->serializer->unserialize(data: $value, decode: false);
        }

        return $value;
    }

    /**
     * Checks if a key exists/is set
     * @param string $key The key
     * @return bool True if the key exists
     */
    public function exists(string $key) : bool
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->driver->exists($key . '-' . $this->key);
    }

    /**
     * Delets $key from the memcache
     * @param string $key The key
     * @return mixed The value for $key
     * @return bool
     */
    public function delete(string $key) : bool
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->driver->delete($key . '-' . $this->key);
    }

    /**
     * Deletes all keys from the memcache server
     * @return static
     */
    public function deleteAll() : static
    {
        if (!$this->enabled) {
            return $this;
        }

        $this->driver->deleteAll();

        return $this;
    }
}
