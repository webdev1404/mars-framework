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
     * Returns the list of existing files in the specified directory
     * @param string $dir The directory to scan for files
     * @return array The list of existing files
     */
    protected function getCachedFiles(string $dir) : array
    {
        $cache_filename = static::$type . '-' . $this->name . '-' . basename($dir) . '-files';

        $files = $this->app->cache->get($cache_filename);

        // Force files scan if we are in development mode
        if ($this->development) {
            $files = null;
        }

        if ($files === null) {
            $files = [];

            if (is_dir($dir)) {
                $files = $this->app->dir->getFiles($dir, true, false);
                $files = $this->app->array->flip($files);
            }

            $this->app->cache->set($cache_filename, $files);
        }

        return $files;
    }
}
