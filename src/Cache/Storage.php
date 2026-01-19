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
     * @var string $driver_name The used driver
     */
    protected string $driver_name {
        get {
            if (isset($this->driver_name)) {
                return $this->driver_name;
            }

            $this->driver_name = $this->app->config->cache->storage->driver ?? $this->app->config->cache->driver;
            if ($this->driver_name != 'file' && $this->driver_name != 'memcache') {
                throw new \Exception('The storage cache driver "' . $this->driver_name . '" is not supported. Supported drivers are: file, memcache');
            }

            return $this->driver_name;
        }
    }

    /**
     * @var string $dir The dir where the data will be cached
     */
    protected string $dir = 'storage';

    /**
     * @var bool $can_hash Whether to hash the filename or not
     */
    protected bool $can_hash = true;

    /**
     * @var array $driver_params The parameters to pass to the driver constructor
     */
    protected array $driver_params = [
        false,                // use files cache
        'cacheable_storage',   // driver type
    ];

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
}
