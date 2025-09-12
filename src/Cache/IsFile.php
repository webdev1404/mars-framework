<?php
/**
* The Is File Cache Trait
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Is File Cache Trait
 * Trait which caches in a single file the files existing in a folder
 */
trait IsFile
{
    /**
     * @var array|null $existing_files The list of cached existing files
     */
    protected ?array $existing_files = null;

    /**
     * @var string $existing_files_file The file where the list of existing files is cached
     */
    protected string $existing_files_file = 'cached-files-list.php';

    /**
     * Checks if a file exists
     * @param string $filename The filename
     * @param string|null $path The path where to look for the file. If null, will use the path of the filename
     * @return bool True if the file exists, false otherwise
     */
    protected function isFile(string $filename, ?string $path = null) : bool
    {
        if ($this->existing_files !== null) {
            return isset($this->existing_files[$filename]);
        }

        $path??= dirname($filename);
        $cache_filename = $path . '/' . $this->existing_files_file;

        if (is_file($cache_filename)) {
            $this->existing_files = require $cache_filename;

            return isset($this->existing_files[$filename]);
        }

        $this->cacheIsFile($path);

        return is_file($filename);
    }

    /**
     * Marks a file as existing
     * @param string $filename The filename
     * @param string|null $path The path where to look for the file
     */
    protected function setIsFile(string $filename, ?string $path = null)
    {
        if ($this->isFile($filename, $path)) {
            return;
        }

        //if we're adding a new file, we need to delete the existing files cache file
        if ($this->existing_files !== null) {
            if (isset($this->existing_files[$filename])) {
                return;
            }

            $path??= dirname($filename);

            unlink($path . '/' . $this->existing_files_file);
        }
    }

    /**
     * Caches the existing files in a folder in a single file
     * @param string $path The path where to look for files
     */
    protected function cacheIsFile(string $path)
    {
        $cache_filename = $path . '/' . $this->existing_files_file;
        $files = $this->app->dir->getFiles($path, false, true);
        
        $content = "<?php\n\nreturn ";
        $content.= var_export(array_fill_keys($files, true), true);
        $content.= ";\n";

        file_put_contents($cache_filename, $content);
    }
}
