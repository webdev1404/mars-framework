<?php
/**
* The Cachable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\App\Kernel;
use Mars\Cache\Cacheable\CacheableInterface;

/**
 * The Cachable File Driver
 * Driver which stores on disk the cached resources
 */
class File implements CacheableInterface
{
    use Kernel;

    /**
     * @see CacheableInterface::get()
     * {@inheritdoc}
     */
    public function get(string $filename) : string
    {
        if (!is_file($filename)) {
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
        return file_put_contents($filename, $content);
    }

    /**
     * @see CacheableInterface::getLastModified()
     * {@inheritdoc}
     */
    public function getLastModified(string $filename) : int
    {
        if (!is_file($filename)) {
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
        if (is_file($filename)) {
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
