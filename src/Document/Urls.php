<?php
/**
* The URLs Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;
use Mars\App\Kernel;
use Mars\Data\IteratorTrait;

/**
 * The Document URLs Class
 * Class containing a list of document URLs
 */
class Urls implements \Countable, \IteratorAggregate
{
    use Kernel;
    use IteratorTrait;

    /**
     * @var string $type The URLs' type
     */
    public protected(set) string $type = '';

    /**
     * @var array The list of URLs
     */
    public protected(set) array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * Builds the Urls object
     * @param string $type The URLs' type
     * @param App|null $app The app instance
     */
    public function __construct(string $type = '', ?App $app = null)
    {
        $this->app = $app;
        $this->type = $type;
    }

    /**
     * Adds an URL to the list
     * @param string|array|Url|Urls $url The URL to add
     * @param array $attributes The URL's attributes, if any
     * @param int $priority The URL's priority (higher number means higher priority)
     * @return static
     */
    public function add(string|array|Url|Urls $url, array $attributes = [], int $priority = 100) : static
    {
        if ($url instanceof Urls) {
            return $this->addMany($url);
        }

        if (!$url instanceof Url) {
            $url = new Url($url, $this->type, $attributes, $priority, $this->app);
        }

        $this->urls[] = $url;

        return $this;
    }

    /**
     * Adds multiple URLs to the list
     * @param array|Urls $urls The URLs to add
     * @return static
     */
    public function addMany(array|Urls $urls) : static
    {
        if ($urls instanceof Urls) {
            $this->urls = array_merge($this->urls, $urls->urls);
        } else {
            foreach ($urls as $url) {
                $this->add($url);
            }
        }

        return $this;
    }

    /**
     * Removes a url from the list
     * @param string|array|Url|Urls $url The URL to remove
     * @return static
     */
    public function remove(string|array|Url $url) : static
    {
        if (!$url instanceof Urls) {
            $url = new Url($url, $this->type, app: $this->app);
        }

        $key = array_find_key($this->urls, fn($current_url) => $current_url->url == $url->url);
        if ($key !== null) {
            unset($this->urls[$key]);
        }

        return $this;
    }

    /**
     * Sorts the URLs by priority
     * @return static
     */
    public function sort() : static
    {
        usort($this->urls, function ($url1, $url2) {
            return $url2->priority <=> $url1->priority;
        });

        return $this;
    }

    /**
     * Returns the list of external urls
     * @return Urls The list of external urls
     */
    public function getExternal() : Urls
    {
        $urls = array_filter($this->urls, fn($url) => !$url->is_local);
        
        return new static($this->type, $this->app)->set($urls);
    }

    /**
     * Returns the list of local urls
     * @return Urls The list of local urls
     */
    public function getLocal() : Urls
    {
        $urls = array_filter($this->urls, fn($url) => $url->is_local);
        
        return new static($this->type, $this->app)->set($urls);
    }
}
