<?php
/**
* The Cache Class
* @package Mars
*/

namespace Mars;

use Mars\App\LazyLoad;
use Mars\Cache\Css;
use Mars\Cache\Javascript;
use Mars\Cache\Data;
use Mars\Cache\Pages;
use Mars\Cache\Routes;
use Mars\Cache\Templates;

/**
 * The Cache Class
 * Handles the caching of system values and provides functionality for interacting with cached data.
 */
#[\AllowDynamicProperties]
class Cache
{
    use LazyLoad;

    /**
     * @var Css $css The Css Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Css $css;

    /**
     * @var Javascript $javascript The Javascript Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Javascript $javascript;

    /**
     * @var Page Data $data The Data Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Data $data;

    /**
     * @var Page $page The Page Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Pages $pages;

    /**
     * @var Routes $routes The Routes Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Routes $routes;

    /**
     * @var Templates $templates The Templates Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Templates $templates;

    /**
     * Builds the Cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);
    }

    /**
     * Gets a cached value
     * @param string $name The name of the value to get
     */
    public function get(string $name)
    {
        return $this->data->get($name);
    }

    /**
     * Sets The value of a cached value
     * @param string $name The name
     * @param mixed $value The value
     */
    public function set(string $name, $value) : static
    {
        $this->data->set($name, $value);

        return $this;
    }

    /**
     * Deletes a cached value
     * @param string $name The name of the value to unset
     */
    public function delete(string $name) : static
    {
        $this->data->delete($name);

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
        return $this->data->getArray($filename, $hash_filename);
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
        $this->data->setArray($filename, $data, $hash_filename);

        return $this;
    }

    /**
     * Deletes a cached php file
     * @param string $filename The name of the file
     * @param bool $hash_filename Whether to hash the filename or not
     * @return static $this
     */
    public function deleteArray(string $filename, bool $hash_filename = true) : static
    {
        $this->data->deleteArray($filename, $hash_filename);

        return $this;
    }
}
