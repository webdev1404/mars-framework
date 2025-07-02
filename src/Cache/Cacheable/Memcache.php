<?php
/**
* The Cachable Memcache Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\App\Kernel;
use Mars\Cache\Cacheable\CacheableInterface;

/**
 * The Cachable Memcache Driver
 * Driver which stores in memcache the cached resources
 */
class Memcache implements CacheableInterface
{
    use Kernel;

    /**
     * Checks if memcache is enabled
     * @throws \Exception
     */
    protected function check()
    {
        if (!$this->app->memcache->enabled) {
            throw new \Exception('Memcache must be enabled to be able to use the memcache cachable driver');
        }
    }

    /**
     * Returns the filename without the cache path
     * @param string $filename The filename
     * @return string The filename
     */
    protected function getFilename(string $filename) : string
    {
        return str_replace($this->app->cache_path . '/', '', $filename);
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritdoc}
     */
    public function get(string $filename) : string
    {
        $this->check();

        return $this->app->memcache->get($this->getFilename($filename));
    }

    /**
     * @see CacheableInterface::store()
     * {@inheritdoc}
     */
    public function store(string $filename, string $content, string $type) : bool
    {        
        $this->check();

        $filename = $this->getFilename($filename);

        $this->app->memcache->set($filename, $content);
        $this->app->memcache->set($filename . '-last-modified', time());
        $this->app->memcache->storeKey($filename, $type);

        return true;
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritdoc}
     */
    public function getLastModified(string $filename) : int
    {
        $this->check();

        $filename = $this->getFilename($filename);

        return (int)$this->app->memcache->get($filename . '-last-modified');
    }

    /**
     * @see CacheableInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $filename, string $type) : bool
    {
        $this->check();

        $filename = $this->getFilename($filename);

        $this->app->memcache->delete($filename);
        $this->app->memcache->delete($filename . '-last-modified');
        $this->app->memcache->deleteKey($filename, $type);

        return true;
    }

    /**
     * @see CacheableInterface::clean()
     * {@inheritdoc}
     */
    public function clean(string $dir, string $type)
    {
        $this->check();

        $keys = $this->app->memcache->getKeys($type);
        if (!$keys) {
            return;
        }

        foreach ($keys as $key) {
            $this->app->memcache->delete($key);
            $this->app->memcache->delete($key . '-last-modified');
        }

        $this->app->memcache->deleteKeyEntry($type);
    }
}
