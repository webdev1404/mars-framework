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
 * Driver which stores the cached data as a file on the disk
 */
abstract class File extends Base implements CacheableInterface
{
    use IsFileTrait;

    /**
     * @var string $extension The extension to use for the cached files
     */
    protected string $extension = '';

    /**
     * Returns the filename used to store the cached content
     * @param string $filename The original filename
     * @return string The filename used to store the cached content
     */
    protected function getFilename(string $filename) : string
    {
        if ($this->extension) {
            $filename .= '.' . $this->extension;
        }

        return $filename;
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritDoc}
     */
    public function get(string $filename) : mixed
    {
        $filename = $this->getFilename($filename);

        if (!$this->isFile($filename)) {
            return null;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return null;
        }

        return $content;
    }

    /**
     * @see CacheableInterface::set()
     * {@inheritDoc}
     */
    public function set(string $filename, mixed $content) : bool
    {
        $filename = $this->getFilename($filename);

        $this->setIsFile($filename);

        return file_put_contents($filename, (string)$content);
    }

    /**
     * @see CacheableInterface::create()
     * {@inheritDoc}
     */
    public function create(string $filename) : bool
    {
        return $this->set($filename, '');
    }

    /**
     * @see CacheableInterface::has()
     * {@inheritDoc}
     */
    public function has(string $filename) : bool
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
    public function clean(string $path, ?int $expire_hours = null)
    {
        if (!$expire_hours) {
            $this->app->dir->clean($path);
        } else {
            $this->app->dir->cleanExpired($path, time() - ($expire_hours * 3600));
        }
    }
}
