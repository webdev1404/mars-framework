<?php
/**
* The Memcached Memcache Class
* @package Mars
*/

namespace Mars\Memcache;

/**
 * The Memcached Memcache Class
 * Memcache driver which uses Memcached
 */
class Memcached implements MemcacheInterface
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
        if (!extension_loaded('memcached')) {
            throw new \Exception("The memcached extension isn't available on this server. Either install it or disable it's use by changing 'memcache_enable' to false in config.php");
        }

        $this->handle = new \Memcached;

        if (!$this->handle->addServer($host, $port) || !$this->handle->getStats()) {
            throw new \Exception('Error connecting to the memcached server');
        }        
    }

    /**
     * @see MemcacheInterface::disconnect()
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if (isset($this->handle)) {
            unset($this->handle);
        }
    }

    /**
     * @see MemcacheInterface::add()
     * {@inheritdoc}
     */
    public function add(string $key, $value, int $expires = 0) : bool
    {
        return $this->handle->add($key, serialize($value), $expires);
    }

    /**
     * @see MemcacheInterface::set()
     * {@inheritdoc}
     */
    public function set(string $key, $value, int $expires = 0) : bool
    {
        return $this->handle->set($key, serialize($value), $expires);
    }

    /**
     * @see MemcacheInterface::get()
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        $value = $this->handle->get($key);
        if (!$value) {
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
        if ($this->handle->get($key) === false) {
            return false;
        }

        return true;
    }

    /**
     * @see MemcacheInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $key) : bool
    {
        return $this->handle->delete($key);
    }

    /**
     * @see MemcacheInterface::deleteAll()
     * {@inheritdoc}
     */
    public function deleteAll() : bool
    {
        return $this->handle->flush();
    }
}
