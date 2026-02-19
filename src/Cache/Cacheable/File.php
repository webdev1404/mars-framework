<?php
/**
* The Cacheable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\Cache\Cacheable\CacheableInterface;
use Mars\Filesystem\IsFileTrait;

/**
 * The Cacheable File Driver
 * Driver which stores on disk the cached resources
 */
class File extends Base implements CacheableInterface
{
    use IsFileTrait;

    /**
     * Returns the filename used to store the cached content
     * @param string $filename The original filename
     * @return string The filename used to store the cached content
     */
    protected function getFilename(string $filename) : string
    {
        return $filename;
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritDoc}
     */
    public function get(string $filename, bool $unserialize) : mixed
    {
        $filename = $this->getFilename($filename);

        if (!$this->isFile($filename)) {
            return null;
        }

        $content = file_get_contents($filename);
        if (!$unserialize) {
            return $content;
        }

        return $this->app->serializer->unserializeData($content);
    }

    /**
     * @see CacheableInterface::set()
     * {@inheritDoc}
     */
    public function set(string $filename, mixed $content, bool $serialize) : bool
    {
        $filename = $this->getFilename($filename);

        $this->setIsFile($filename);

        if ($serialize) {
            $content = $this->app->serializer->serializeData($content);
        }

        return file_put_contents($filename, $content);
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

        return $this->isFile($filename);
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritDoc}
     */
    public function getLastModified(string $filename) : int
    {
        $filename = $this->getFilename($filename);

        if (!$this->isFile($filename)) {
            return 0;
        }

        return filemtime($filename);
    }

    /**
     * @see CacheableInterface::delete()
     * {@inheritDoc}
     */
    public function delete(string $filename) : bool
    {
        $filename = $this->getFilename($filename);
        if ($this->isFile($filename)) {
            $this->deleteIsFileCache(dirname($filename));

            return unlink($filename);
        }

        return true;
    }

    /**
     * @see CacheableInterface::clean()
     * {@inheritDoc}
     */
    public function clean(string $dir, ?int $expire_hours = null)
    {
        if (!$expire_hours) {
            $this->app->dir->clean($dir);
        } else {
            $this->app->dir->cleanExpired($dir, time() - ($expire_hours * 3600));
        }
    }
}
