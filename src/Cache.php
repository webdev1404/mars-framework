<?php
/**
* The Cache Class
* @package Mars
*/

namespace Mars;

use Mars\Cache\Cacheable;
use Mars\Cache\Pages;
use Mars\Cache\Templates;
use Mars\Data\PropertiesTrait;
use Mars\Lazyload\GhostTrait;

/**
 * The Cache Class
 * Stores the system's cached values & contains the functionality for interacting with the system's cached data
 * Not to be confused with Cachable or Caching
 */
#[\AllowDynamicProperties]
class Cache extends Cacheable
{
    use GhostTrait;
    use PropertiesTrait;

    /**
     * @var Page $page The Page Cache object
     */
    #[LazyLoad]
    public protected(set) Pages $pages;

    /**
     * @var Templates $templates The Templates Cache object
     */
    #[LazyLoad]
    public protected(set) Templates $templates;

    /**
     * @var string $path The folder where the content will be cached
     */
    protected string $path {
        get => $this->app->cache_path . '/data';
    }

    /**
     * @var string $driver The used driver
     */
    protected string $driver_name {
        get => $this->app->config->cache_driver;
    }

    /**
     * @var string $file The name of the file used to cache the content
     */
    protected string $file = 'cache-data';

    /**
     * @var string $extension The extension of the cache file
     */
    protected string $extension = 'json';

    /**
     * @var array $data The cached data
     */
    protected array $data = [];

    /**
     * @var bool $loaded If true, the data has been loaded
     */
    protected bool $loaded = false;

    /**
     * Builds the Cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        parent::__construct($app);
    }

    /**
     * Gets a cached value
     * @param string $name The name of the value to get
     */
    public function get(string $name)
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->data[$name] ?? null;
    }

    /**
     * Sets The value of a cached value
     * @param string $name The name 
     * @param mixed $value The value
     */
    public function set(string $name, $value) : static
    {
        $this->data[$name] = $value;

        $this->store();

        return $this;
    }

    /**
     * Gets the cached data from a file
     * @param string $filename The name of the file
     * @return mixed The cached data
     */
    public function getFile(string $filename)
    {
        $filename = $this->getFilename($filename);

        $data = $this->driver->get($filename);
   
        if (!$data) {
            return null;
        }

        return json_decode($data, true);
    }

    /**
     * Stores data to a file
     */
    public function setFile(string $filename, $data) : static
    {
        $filename = $this->getFilename($filename);

        $this->driver->store($filename, json_encode($data));

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
     * Unsets a cached value
     * @param string $name The name of the value to unset
     */
    public function unset(string $name) : static
    {
        unset($this->data[$name]);

        $this->store();

        return $this;
    }

    /**
     * Loads the cached data
     */
    protected function load()
    {
        $this->loaded = true;

        $data = $this->driver->get($this->filename);
        if (!$data) {
            return null;
        }

        $this->data = json_decode($data, true);
    }

    /**
     * Stores the cached data
     */
    public function store()
    {       
        $this->driver->store($this->filename, json_encode($this->data));
    }
}
