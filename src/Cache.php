<?php
/**
* The Cache Class
* @package Mars
*/

namespace Mars;

use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Cache\Assets;
use Mars\Cache\Config;
use Mars\Cache\Assets\Lists\Css as CssList;
use Mars\Cache\Assets\Lists\Javascript as JavascriptList;
use Mars\Cache\Assets\Urls\Css;
use Mars\Cache\Assets\Urls\Javascript;
use Mars\Cache\Data;
use Mars\Cache\Pages;
use Mars\Cache\Routes;
use Mars\Cache\Storage;
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
     * @var Config $config The Config Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Config $config;

    /**
     * @var CssList $css_list The Css List Cache object
     */
    #[LazyLoadProperty]
    public protected(set) CssList $css_list;

    /**
     * @var JavascriptList $js_list The Javascript List Cache object
     */
    #[LazyLoadProperty]
    public protected(set) JavascriptList $js_list;

    /**
     * @var Css $css The Css Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Css $css;

    /**
     * @var Javascript $js The Javascript Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Javascript $js;

    /**
     * @var Data $data The Data Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Data $data;

    /**
     * @var Pages $pages The Page Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Pages $pages;

    /**
     * @var Routes $routes The Routes Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Routes $routes;

    /**
     * @var Storage $storage The Storage Cache object
     */
    #[LazyLoadProperty]
    public protected(set) Storage $storage;

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
     * @return mixed The cached value or null if it does not exist
     */
    public function get(string $name) : mixed
    {
        return $this->storage->get($name);
    }

    /**
     * Sets the value of a cached value
     * @param string $name The name
     * @param mixed $value The value
     */
    public function set(string $name, mixed $value) : static
    {
        $this->storage->set($name, $value);

        return $this;
    }

    /**
     * Deletes a cached value
     * @param string $name The name of the value to unset
     */
    public function delete(string $name) : static
    {
        $this->storage->delete($name);

        return $this;
    }
}
