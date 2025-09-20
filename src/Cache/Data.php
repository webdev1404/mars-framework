<?php
/**
* The Data Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Filesystem\IsFile;

/**
 * The Data Cache Class
 * Class which handles the caching of data
 */
class Data extends Cacheable
{
    use IsFile;

    /**
     * @var string $dir The dir where the data will be cached
     */
    protected string $dir = 'data';

    /**
     * @var string $extension The extension of the cache file
     */
    protected string $extension = 'json';

    /**
     * Gets a cached value
     * @param string $name The name of the value to get
     */
    public function get(string $name)
    {
        $filename = $this->getFilename($name, $this->extension);

        $data = $this->driver->get($filename, $this->dir);
        if (!$data) {
            return null;
        }

        return json_decode($data, true);
    }

    /**
     * Sets The value of a cached value
     * @param string $name The name
     * @param mixed $value The value
     * @return static $this
     */
    public function set(string $name, $value) : static
    {
        $filename = $this->getFilename($name, $this->extension);

        $this->driver->store($filename, json_encode($value), $this->dir);

        return $this;
    }

    /**
     * Deletes a cached value
     * @param string $name The name of the value to unset
     * @return static $this
     */
    public function delete(string $name) : static
    {
        $filename = $this->getFilename($name, $this->extension);

        $this->driver->delete($filename, $this->dir);

        return $this;
    }

    /**
     * Gets an array from a php file
     * @param string $filename The name of the file
     * @param bool $hash_filename Whether to hash the filename or not
     * @return array The array or null if the file does not exist
     */
    public function getArray(string $filename, bool $hash_filename = true) : ?array
    {
        $filename = $this->getFilename($filename, 'php', $hash_filename);
        if (!$this->isFile($filename, $this->path)) {
            return null;
        }

        return include $filename;
    }
    
    /**
     * Stores an array to a php file
     * @param string $filename The name of the file
     * @param bool $hash_filename Whether to hash the filename or not
     * @param array $data The data to store
     * @return static $this
     */
    public function setArray(string $filename, array $data, bool $hash_filename = true) : static
    {
        $filename = $this->getFilename($filename, 'php', $hash_filename);

        $content = "<?php\n\nreturn ";
        $content.= var_export($data, true);
        $content.= ";\n";
        
        file_put_contents($filename, $content);

        $this->setIsFile($filename, $this->path);

        return $this;
    }

    /**
     * Deletes a cached array file
     * @param string $filename The name of the file
     * @param bool $hash_filename Whether to hash the filename or not
     * @return static $this
     */
    public function deleteArray(string $filename, bool $hash_filename = true) : static
    {
        $filename = $this->getFilename($filename, 'php', $hash_filename);

        if ($this->isFile($filename, $this->path)) {
            unlink($filename);
        }

        return $this;
    }

    /**
     * Gets the filename for a cache file
     * @param string $filename The name of the file
     * @param string|null $extension The extension of the file. If null, $this->extension will be used
     * @param bool $hash_filename Whether to hash the filename or not
     * @return string The filename
     */
    protected function getFilename(string $filename, ?string $extension = null, bool $hash_filename = true) : string
    {
        return $this->path . '/' . $this->getName($filename, $extension, $hash_filename);
    }

    /**
     * Cleans the data cache
     */
    public function clean() : static
    {
        $this->driver->clean($this->path, $this->dir);

        //clean the cache dir, if the driver is not file, to clean the files set with setArray
        if ($this->driver_name != 'file') {
            $this->app->dir->clean($this->path);
        }
        
        return $this;
    }
}
