<?php
/**
* The Files Cache Trait
* @package Mars
*/

namespace Mars\Extensions\Abilities;

/**
 * The Files Cache Trait
 * Trait which allows extensions to cache files
 */
trait FilesCacheTrait
{
    /**
     * @var array $files_cache_list The list of cached files
     */
    protected array $files_cache_list {
        get {
            if (isset($this->files_cache_list)) {
                return $this->files_cache_list;
            }

            $this->files_cache_list = $this->getCachedFiles();

            return $this->files_cache_list;
        }
    }

    /**
     * Returns the list of existing files in the specified directory
     * @param string $dir The directory to scan for files
     * @return array The list of existing files
     */
    protected function getCachedFiles() : array
    {
        $cache_filename = static::$type . '-' . $this->name . '-files';

        $files = $this->app->cache->get($cache_filename);

        // Force files scan if we are in development mode
        if ($this->development) {
            $files = null;
        }

        if ($files === null) {
            $files = [];


            foreach (static::CACHE_DIRS as $name) {
                $dir = $this->path . '/' . $name;
                if (!is_dir($dir)) {
                    continue;
                }

                $files[$name] = $this->app->dir->getFiles($dir, true, false);
                $files[$name] = $this->app->array->flip($files[$name]);
            }

            $this->app->cache->set($cache_filename, $files);
        }

        return $files;
    }
}
