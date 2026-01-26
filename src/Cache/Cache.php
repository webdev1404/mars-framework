<?php
/**
* The Base Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Base Cache Class
 * Base class for all cache classes
 */
abstract class Cache implements CacheInterface
{
    use Kernel;

    /**
     * @var string $dir The dir where the data will be cached
     */
    public protected(set) string $dir = '';

    /**
     * @var string $path The folder where the data will be cached
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }

            $this->path = $this->app->cache_path . '/' . $this->dir;

            return $this->path;
        }
    }

    /**
     * Cleans all the cached data
     */
    public function clean()
    {
        $this->app->dir->clean($this->path);
    }
}
