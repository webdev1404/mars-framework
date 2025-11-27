<?php
/**
* The Cachable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\Cache\Cacheable\CacheableInterface;
use Mars\Filesystem\IsFileTrait;

/**
 * The Cachable File Driver
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function create(string $filename)
    {
        $this->set($filename, '', false);
    }

    /**
     * @see CacheableInterface::exists()
     * {@inheritdoc}
     */
    public function exists(string $filename) : bool
    {
        $filename = $this->getFilename($filename);

        return $this->isFile($filename);
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function delete(string $filename) : bool
    {
        $filename = $this->getFilename($filename);
        if ($this->isFile($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * @see CacheableInterface::clean()
     * {@inheritdoc}
     */
    public function clean(string $dir)
    {
        $this->app->dir->clean($dir);
    }
}
