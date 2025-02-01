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
            return;
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
