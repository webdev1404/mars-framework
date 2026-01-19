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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function set(string $key, $value, int $expires = 0) : bool
    {
        return $this->add($key, $value, $expires);
    }

    /**
     * @see MemcacheInterface::get()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function exists(string $key) : bool
    {
        return (bool) $this->handle->exists($key);
    }

    /**
     * @see MemcacheInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $key) : bool
    {
        return $this->handle->del($key);
    }

    /**
     * @see MemcacheInterface::deleteAll()
     * {@inheritdoc}
     */
    public function deleteAll() : bool
    {
        return $this->handle->flushAll();
    }
}
