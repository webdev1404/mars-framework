<?php
/**
* The Memcache Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Memcache\MemcacheInterface;

/**
 * The Memcache Class
 * Handles the interactions with the memory cache.
 * Not the same as the memcache extension, although it might use it as a driver
 */
class Memcache
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'redis' => \Mars\Memcache\Redis::class,
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

            $this->drivers = new Drivers($this->supported_drivers, MemcacheInterface::class, 'memcache', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var MemcacheInterface $driver The driver object
     */
    public protected(set) ?MemcacheInterface $driver {
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
        get {
            if (isset($this->key)) {
                return $this->key;
            }

            $this->key = $this->app->config->memcache_key;
            if (!$this->key && $this->enabled) {
                throw new \Exception('The memcache_key config option must be set if memcache is enabled');
            }

            return $this->key;
        }
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
     * Checks if memcache is enabled
     * @throws \Exception If memcache is not enabled
     */
    protected function check()
    {
        if (!$this->enabled) {
            throw new \Exception('Memcache must be enabled to be able to use it. Please check the \'memcache_enable\' config option.');
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
        $this->check();

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
        $this->check();
    
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
        $this->check();

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
        $this->check();

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
        $this->check();

        return $this->driver->delete($key . '-' . $this->key);
    }

    /**
     * Deletes all keys from the memcache server
     * @return static
     */
    public function deleteAll() : static
    {
        $this->check();

        $this->driver->deleteAll();

        return $this;
    }

    /**
     * Gets all keys of a certain type
     * @param string $type The type of the keys
     * @return array The keys
     */
    public function getKeys(string $type) : array
    {
        $keys = $this->get("{$type}-all-keys") ?? [];
        if ($keys) {
            $keys = (array)json_decode($keys);
        }

        return $keys;
    }

    /**
     * Stores the key in the cache, so we know which keys are used
     * @param string $key The key to store
     */
    public function storeKey(string $key, string $type) : static
    {
        $keys = $this->getKeys($type);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
        }

        $this->set("{$type}-all-keys", json_encode($keys));

        return $this;
    }

    /**
     * Deletes a key from the list of keys
     * @param string $key The key to delete
     * @param string $type The type of the key
     * @return static
     */
    public function deleteKey(string $key, string $type) : static
    {
        $keys = $this->getKeys($type);
        $key_index = array_search($key, $keys);
        if ($key_index !== false) {
            unset($keys[$key_index]);
        }

        $this->set("{$type}-all-keys", json_encode($keys));

        return $this;
    }

    /**
     * Deletes the key entry itself
     * @param string $type The type of the keys
     * @return static
     */
    public function deleteKeyEntry(string $type) : static
    {
        $this->delete("{$type}-all-keys");

        return $this;
    }

    public function deleteData(string $type) : static
    {
        $keys = $this->getKeys($type);
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return $this;
    }
}
