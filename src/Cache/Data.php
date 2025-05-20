<?php
/**
* The Data Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Bin\Cache;

/**
 * The Data Cache Class
 * Class which handles the caching of data
 */
class Data extends Cacheable
{
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
     * @return array The array
     */
    public function getArray(string $filename) : array
    {
        $filename = $this->getFilename($filename, 'php');
        if (!is_file($filename)) {
            return [];
        }

        return include $filename;
    }
    
    /**
     * Stores an array to a php file
     * @param string $filename The name of the file
     * @param array $data The data to store
     * @return static $this
     */
    public function setArray(string $filename, array $data) : static
    {
        $filename = $this->getFilename($filename, 'php');
        
        $content = "<?php\n\nreturn [\n";
        foreach ($data as $key => $value) {
            $content.= "    '{$key}' => '{$value}',\n";
        }
        
        $content.= "];\n";
        
        file_put_contents($filename, $content);

        return $this;
    }

    /**
     * Gets the filename for a cache file
     * @param string $filename The name of the file
     * @param string|null $extension The extension of the file. If null, $this->extension will be used
     * @return string The filename
     */
    protected function getFilename(string $filename, ?string $extension = null) : string
    {
        return $this->path . '/' . $this->getName($filename, $extension);
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