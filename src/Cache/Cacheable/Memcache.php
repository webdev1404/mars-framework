<?php
/**
* The Cacheable Memcache Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\Cache\Cacheable\CacheableInterface;

/**
 * The Cacheable Memcache Driver
 * Driver which stores in memcache the cached resources
 */
class Memcache extends Base implements CacheableInterface
{
    /**
     * Returns the filename without the cache path
     * @param string $filename The filename
     * @return string The filename
     */
    protected function getFilename(string $filename) : string
    {
        return str_replace($this->app->base_path . '/', '', $filename);
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritDoc}
     */
    public function get(string $filename, bool $unserialize) : mixed
    {
        $filename = $this->getFilename($filename);
        
        $content = $this->app->memcache->get($filename);
        if (!$content) {
            return $content;
        }

        return $this->app->serializer->unserializeData($content);
    }

    /**
     * @see CacheableInterface::store()
     * {@inheritDoc}
     */
    public function set(string $filename, mixed $content, bool $serialize) : bool
    {
        $filename = $this->getFilename($filename);

        $this->app->memcache->set($filename, $this->app->serializer->serializeData($content));
        $this->app->memcache->set($filename . '-last-modified', time());
        $this->app->memcache->storeKey($filename, $this->type);

        return true;
    }

    /**
     * @see CacheableInterface::create()
     * {@inheritDoc}
     */
    public function create(string $filename) : bool
    {
        return $this->set($filename, '', false);
    }

    /**
     * @see CacheableInterface::exists()
     * {@inheritDoc}
     */
    public function exists(string $filename) : bool
    {
        $filename = $this->getFilename($filename);

        return $this->app->memcache->exists($filename);
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritDoc}
     */
    public function getLastModified(string $filename) : int
    {
        $filename = $this->getFilename($filename);

        return (int)$this->app->memcache->get($filename . '-last-modified');
    }

    /**
     * @see CacheableInterface::delete()
     * {@inheritDoc}
     */
    public function delete(string $filename) : bool
    {
        $filename = $this->getFilename($filename);

        $this->app->memcache->delete($filename);
        $this->app->memcache->delete($filename . '-last-modified');
        $this->app->memcache->deleteKey($filename, $this->type);

        return true;
    }

    /**
     * @see CacheableInterface::clean()
     * {@inheritDoc}
     */
    public function clean(string $dir)
    {
        $keys = $this->app->memcache->getKeys($this->type);
        if (!$keys) {
            return;
        }

        foreach ($keys as $key) {
            $this->app->memcache->delete($key);
            $this->app->memcache->delete($key . '-last-modified');
        }

        $this->app->memcache->deleteKeyEntry($this->type);
    }
}
