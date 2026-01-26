<?php
/**
* The Redis Memcache Class
* @package Mars
*/

namespace Mars\Memcache;

/**
 * The Redis Memcache Class
 * Memcache driver which uses Redis
 */
class Redis implements MemcacheInterface
{
    /**
     * @var object $handle The driver's handle
     */
    protected object $handle;

    /**
     * @see MemcacheInterface::connect()
     * {@inheritDoc}
     */
    public function connect(string $host, string $port)
    {
        if (!class_exists('\\Redis')) {
            throw new \Exception("The redis extension isn't available on this server. Either install it or disable its use by changing 'memcache_enable' to false in config.php");
        }

        $this->handle = new \Redis;

        if (!$this->handle->connect($host, $port)) {
            throw new \Exception('Error connecting to the redis server');
        }
    }

    /**
     * @see MemcacheInterface::disconnect()
     * {@inheritDoc}
     */
    public function disconnect()
    {
        if (isset($this->handle)) {
            $this->handle->close();

            unset($this->handle);
        }
    }

    /**
     * @see MemcacheInterface::add()
     * {@inheritDoc}
     */
    public function add(string $key, $value, int $expires = 0) : bool
    {
        $result = $this->handle->set($key, serialize($value));

        if ($expires) {
            $this->handle->expireAt($key, time() + $expires);
        }

        return $result;
    }

    /**
     * @see MemcacheInterface::set()
     * {@inheritDoc}
     */
    public function set(string $key, $value, int $expires = 0) : bool
    {
        return $this->add($key, $value, $expires);
    }

    /**
     * @see MemcacheInterface::get()
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        $value = $this->handle->get($key);
        if ($value === false) {
            return null;
        }

        return unserialize($value);
    }

    /**
     * @see MemcacheInterface::exists()
     * {@inheritDoc}
     */
    public function exists(string $key) : bool
    {
        return (bool) $this->handle->exists($key);
    }

    /**
     * @see MemcacheInterface::delete()
     * {@inheritDoc}
     */
    public function delete(string $key) : bool
    {
        return $this->handle->del($key);
    }

    /**
     * @see MemcacheInterface::deleteAll()
     * {@inheritDoc}
     */
    public function deleteAll() : bool
    {
        return $this->handle->flushAll();
    }
}
