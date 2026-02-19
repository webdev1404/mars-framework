<?php
/**
* The Storage Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Storage Cache Class
 * Class which handles the caching of user/app storage data
 */
class Storage extends Cacheable
{
    /**
     * @var array $drivers_list The supported drivers list
     */
    public protected(set) array $drivers_list = [
        'file' => \Mars\Cache\Cacheable\File::class,
        'memcache' => \Mars\Cache\Cacheable\Memcache::class,
    ];

    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->storage->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,                // use files cache
        'cacheable_storage',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'storage';

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;

    /**
     * Sets the value of a cached value
     * @param string $name The name
     * @param mixed $value The value
     * @return static $this
     */
    public function set(string $name, mixed $value) : static
    {
        $filename = $this->getFilename($name);
        $dirname = dirname($filename);

        if ($this->driver_name == 'file') {
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }
        }

        $this->driver->set($filename, $value, $this->serialize);

        return $this;
    }

    /**
     * Gets the filename for a cache file
     * @param string $filename The name of the file
     * @return string The filename
     */
    public function getFilename(string $filename) : string
    {
        if (!$this->app->config->cache->storage->subdirs) {
            return parent::getFilename($filename);
        }

        $name = $this->getName($filename);

        $subdirs = [];
        for ($i = 0; $i < $this->app->config->cache->storage->subdir_levels; $i++) {
            $start = $i * $this->app->config->cache->storage->subdir_length;
            $subdirs[] = substr($name, $start, $this->app->config->cache->storage->subdir_length);
        }
        
        $dir = implode('/', $subdirs);

        return $this->path . '/' . $dir . '/' . $name;
    }

    /**
     * Cleans the cache
     */
    public function clean() : static
    {
        $this->driver->clean($this->path, $this->app->config->cache->storage->expire_hours);

        return $this;
    }
}
