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
use Mars\Handlers;

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
     * @var array $store_properties The properties which should be stored
     */
    protected array $store_properties = [];

    /**
     * Builds the Cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        parent::__construct($app);

        $this->load();
    }

    /**
     * Sets The value of a cached value
     * @param string $name The name 
     * @param mixed $value The value
     */
    public function set(string $name, $value) : static
    {
        $this->$name = $value;
        
        $this->store_properties[$name] = true;

        $this->store();

        return $this;
    }

    /**
     * Unsets a cached value
     * @param string $name The name of the value to unset
     */
    public function unset(string $name) : static
    {
        unset($this->$name);
        unset($this->store_properties[$name]);

        $this->store();

        return $this;
    }

    /**
     * Loads the cached data
     */
    protected function load()
    {
        $data = $this->driver->get($this->filename);
        if (!$data) {
            return;
        }

        $data = json_decode($data, true);
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Stores the cached data
     */
    public function store()
    {
        $data = [];
        foreach ($this->store_properties as $name => $value) {
            $data[$name] = $this->$name;
        }

        if (!$data) {
            return;
        }

        $this->driver->store($this->filename, json_encode($data));
    }
}
