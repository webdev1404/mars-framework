<?php
/**
* The Cachable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\App\Kernel;
use Mars\Cache\Cacheable\CacheableInterface;
use Mars\Filesystem\IsFile;

/**
 * The Cachable File Driver
 * Driver which stores on disk the cached resources
 */
class File implements CacheableInterface
{
    use Kernel;
    use IsFile;

    /**
     * @see CacheableInterface::get()
     * {@inheritdoc}
     */
    public function get(string $filename) : string
    {
        if (!$this->isFile($filename)) {
            return '';
        }

        return file_get_contents($filename);
    }

    /**
     * @see CacheableInterface::store()
     * {@inheritdoc}
     */
    public function store(string $filename, string $content, string $type) : bool
    {
        $this->setIsFile($filename);

        return file_put_contents($filename, $content);
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritdoc}
     */
    public function getLastModified(string $filename) : int
    {
        if (!$this->isFile($filename)) {
            return 0;
        }

        return filemtime($filename);
    }

    /**
     * @see CacheableInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $filename, string $type) : bool
    {
        if ($this->isFile($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * @see CacheableInterface::clean()
     * {@inheritdoc}
     */
    public function clean(string $dir, string $type)
    {
        $this->app->dir->clean($dir);
    }
}
