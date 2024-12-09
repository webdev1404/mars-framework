<?php
/**
* The Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App\InstanceTrait;

/**
 * The Document Urls Class
 * Abstract class containing the urls & their corresponding locations used by a document
 */
abstract class Urls
{
    use InstanceTrait;

    /**
     * @var array $urls Array with all the urls to be outputed
     */
    protected array $urls = [];

    /**
     * @var string $version The version to be applied to the urls
     */
    protected string $version = '';

    /**
     * Outputs a preload url
     * @param string $url The url to output
     */
    abstract public function outputPreloadUrl(string $url);

    /**
     * Outputs an url
     * @param string $url The url to output
     * @param array $attributes The attributes of the url, if any
     */
    abstract public function outputUrl(string $url, array $attributes = []);

    /**
     * Loads an url
     * @param string|array $url(s) The url to load. Will only load it once, no matter how many times the function is called with the same url
     * @param string $location The location of the url [head|footer]
     * @param int $priority The url's output priority. The higher, the better
     * @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
     * @param array $attributes The attributes of the url, if any
     * @return static
     */
    public function load(string|array $url, string $location = 'head', int $priority = 100, bool|string $version = true, array $attributes = []) : static
    {
        $urls = (array)$url;

        foreach ($urls as $url) {
            $this->urls[$url] = [
                'location' => $location, 
                'priority' => $priority, 
                'version' => $version,
                'attributes' => $attributes, 
                'is_local' => $this->app->uri->isLocal($url)
            ];
        }

        return $this;
    }

    /**
     * Unloads an url/urls
     * @param string|array $url The url(s) to unload
     * @return static
     */
    public function unload(string|array $url) : static
    {
        $urls = (array)$url;

        foreach ($urls as $url) {
            if (isset($this->urls[$url])) {
                unset($this->urls[$url]);
            }
        }       

        return $this;
    }

    /**
     * Preloads an url
     * @param string|array $url The url(s) to preload
     * @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
     * @return static
     */
    public function preload(string|array $url, bool|string $version = true) : static
    {
        return $this->load($url, 'preload', version: $version);
    }

    /**
     * Returns the list of urls
     * @param string $location The location of the urls [head|footer]
     * @param bool $sort If true, will sort the urls by priority
     * @return array
     */
    public function get(string $location = '', bool $sort = true) : array
    {
        if (!$location) {
            return $this->sort($this->urls);
        }

        $urls = array_filter($this->urls, function ($url) use ($location) {
            if ($url['location'] == $location) {
                return true;
            }

            return false;
        });

        if ($sort) {
            $urls = $this->sort($urls);
        }

        return $urls;
    }

    /**
     * Sorts the urls by priority
     * @param array $urls
     * @return array The sorted urls
     */
    protected function sort(array $urls) : array
    {
        //sort the urls by priority
        uasort($urls, function ($url1, $url2) {
            return $url2['priority'] <=> $url1['priority'];
        });

        return $urls;
    }

    /**
     * Outputs the urls
     * @param string $location The location of the url [head|footer]
     * @return static
     */
    public function output(string $location = 'head') : static
    {
        $urls = $this->get($location);

        foreach ($urls as $url => $data) {
            $this->outputUrl($this->getUrl($url, $data['version']), $data['attributes']);
        }

        return $this;
    }

    public function outputPreload() : static
    {
        $urls = $this->get('preload', false);

        foreach ($urls as $url => $data) {
            $this->outputPreloadUrl($this->getUrl($url, $data['version']));
        }

        return $this;
    }

    /**
     * Returns an url with the version appended, if required
     * @param string $url The url to append the version to
     * @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
     * @return string The url
     */
    public function getUrl(string $url, bool|string $version = true) : string
    {
        if (!$version) {
            return $url;
        }

        if (is_bool($version)) {
            $version = $this->version;
        }

        return $this->app->uri->build($url, ['ver' => $version]);
    }

    /**
     * Merges the attributes and returns the code
     * @param array $attributes The attributes
     * @return string
     */
    protected function getAttributes(array $attributes) : string
    {
        if (!$attributes) {
            return '';
        }

        return ' ' . implode(' ', $attributes);
    }
}
