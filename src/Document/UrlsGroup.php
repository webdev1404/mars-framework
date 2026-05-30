<?php
/**
* The URLs Group Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App\Kernel;
use Mars\Data\ListGroupTrait;

/**
 * The URLs Group Class
 * Class containing a list of document URLs, grouped by type
 */
class UrlsGroup
{
    use Kernel;
    use ListGroupTrait {
        ListGroupTrait::add as listAdd;
        ListGroupTrait::addMany as listAddMany;
    }

    /**
     * @var array The list of URLs
     */
    public protected(set) array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /** 
     * Adds an URL to the list
     * @param string $type The URL's type
     * @param string|array|Url|Urls $url The URL to add
     * @param array $attributes The URL's attributes, if any
     * @param int $priority The URL's priority (higher number means higher priority)
     * @return static
    */
    public function add(string|array $type, string|array|Url $url = '', array $attributes = [], int $priority = 100) : static
    {
        if (is_array($type)) {
            return $this->addMany($type);
        }

        $this->urls[$type] ??= new Urls($type, $this->app);
        $this->urls[$type]->add($url, $attributes, $priority);

        return $this;
    }

    /**
     * Adds multiple URLs to the list
     * @param string|array $type The URL's type
     * @param array $urls The URLs to add
     * @return static
     */
    public function addMany(string|array $type, array $urls = []) : static
    {
        if (is_array($type)) {
            foreach ($type as $_type => $urls) {
                $this->addArray($_type, $urls);
            }
        } else {
            $this->addArray($type, $urls);
        }

        return $this;
    }

    /**
     * Adds multiple URLs to the list
     * @param array $types The URL's types
     * @param array $urls The URLs to add
     */
    protected function addArray(string $type, array $urls)
    {
        $this->urls[$type] ??= new Urls($type, $this->app);
        $this->urls[$type]->addMany($urls);
    }
}
