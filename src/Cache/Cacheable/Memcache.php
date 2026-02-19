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
        if ($content === null) {
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
    public function clean(string $dir, ?int $expire_hours = null)
    {
        $keys = $this->app->memcache->getKeys($this->type);
        if (!$keys) {
            return;
        }

        foreach ($keys as $key) {
            if ($this->canCleanKey($key, $expire_hours)) {
                $this->app->memcache->delete($key);
                $this->app->memcache->delete($key . '-last-modified');
            }
        }

        $this->app->memcache->deleteKeyEntry($this->type);
    }

    /**
     * Determines if a key can be cleaned
     * @param string $key The key
     * @param int|null $expire_hours The expiration in hours
     * @return bool True if can be cleaned, false otherwise
     */
    protected function canCleanKey(string $key, ?int $expire_hours) : bool
    {
        if (!$expire_hours) {
            return true;
        }

        $cutoff = time() - ($expire_hours * 3600);
        $last_modified = $this->app->memcache->get($key . '-last-modified');

        return $last_modified < $cutoff;
    }
}
