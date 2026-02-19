<?php
/**
* The Is File Trait
* @package Mars
*/

namespace Mars\Filesystem;

/**
 * The Is File Trait
 * Trait which caches in a single file the files existing in a folder, to avoid multiple is_file calls
 */
trait IsFileTrait
{
    /**
     * @var bool $files_cache_use Whether to use the existing files cache
     */
    protected bool $files_cache_use {
        get {
            if (isset($this->files_cache_use)) {
                return $this->files_cache_use;
            }

            $this->files_cache_use = $this->app->config->files->cache->use;

            return $this->files_cache_use;
        }
    }

    /**
     * @var array|null $files_cache_list The list of cached existing files
     */
    protected ?array $files_cache_list = null;

    /**
     * @var string $files_cache_file The file where the list of existing files is cached
     */
    protected string $files_cache_file = 'cached-files-list.php';

    /**
     * Checks if a file exists
     * @param string $filename The filename
     * @param string|null $path The path where to look for the file. If null, will use the path of the filename
     * @return bool True if the file exists, false otherwise
     */
    protected function isFile(string $filename, ?string $path = null) : bool
    {
        if (!$this->files_cache_use) {
            return is_file($filename);
        }

        if ($this->files_cache_list !== null) {
            return isset($this->files_cache_list[$filename]);
        }

        $path??= dirname($filename);
        $cache_filename = $path . '/' . $this->files_cache_file;

        if (!is_file($cache_filename)) {
            $this->cacheIsFile($path);
        }
        
        $this->files_cache_list = require $cache_filename;

        return isset($this->files_cache_list[$filename]);
    }

    /**
     * Marks a file as existing
     * @param string $filename The filename
     * @param string|null $path The path where to look for the file
     */
    protected function setIsFile(string $filename, ?string $path = null)
    {
        if (!$this->files_cache_use) {
            return;
        }

        //if we're adding a new file, we need to delete the existing files cache file
        if ($this->files_cache_list !== null) {
            if (isset($this->files_cache_list[$filename])) {
                return;
            }

            $path??= dirname($filename);
            $cache_filename = $path . '/' . $this->files_cache_file;

            if (is_file($cache_filename)) {
                unlink($cache_filename);
            }

            $this->files_cache_list = null;
        }
    }

    /**
     * Deletes the existing files cache file
     * @param string $path The path where to look for the cache file
     */
    protected function deleteIsFileCache(string $path)
    {
        if (!$this->files_cache_use) {
            return;
        }

        $cache_filename = $path . '/' . $this->files_cache_file;

        if (is_file($cache_filename)) {
            unlink($cache_filename);
        }

        $this->files_cache_list = null;
    }

    /**
     * Caches the existing files in a folder in a single file
     * @param string $path The path where to look for files
     */
    protected function cacheIsFile(string $path)
    {
        if (!$this->files_cache_use) {
            return;
        }

        $cache_filename = $path . '/' . $this->files_cache_file;
        $files = $this->app->dir->getFiles($path, false, true);
        
        $content = "<?php\n\nreturn ";
        $content.= var_export(array_fill_keys($files, true), true);
        $content.= ";\n";

        file_put_contents($cache_filename, $content);
    }
}
