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
     * @var string $version The version to be applied to the urls
     */
    public protected(set) string $version = '';

    /**
     * @var string $type The type of the preload
     */
    public protected(set) string $type = '';

    /**
     * @var string $crossorigin The crossorigin attribute of the url
     */
    public protected(set) string $crossorigin = '';

    /**
     * @var array $urls Array with all the urls to be outputed
     */
    public protected(set) array $urls = [];

    /**
     * Outputs an url
     * @param string $url The url to output
     * @param array $attributes The attributes of the url, if any
     */
    abstract public function outputUrl(string $url, array $attributes = []);

    /**
     * Loads an url
     * @param string|array $url(s) The url to load. Will only load it once, no matter how many times the function is called with the same url
     * @param string $type The type of the url [head|footer]
     * @param int $priority The url's output priority. The higher, the better
     * @param bool $early_hints If true, will output the url as an early hint
     * @param array $attributes The attributes of the url, if any
     * @return static
     */
    public function load(string|array $url, string $type = 'head', int $priority = 100, bool $early_hints = false, array $attributes = []) : static
    {
        $urls = (array)$url;

        foreach ($urls as $url) {
            $full_url = $url;
            if ($this->version && $this->app->uri->isLocal($url)) {                
                $full_url = $this->app->uri->build($url, ['ver' => $this->version]);
            }

            if ($early_hints) {
                //add the url as an 103 Early Hints header
            }

            $this->urls[$type][$url] = [
                'url' => $full_url,
                'priority' => $priority, 
                'attributes' => $attributes, 
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
            foreach ($this->urls as $type => $urls_array) {
                if (isset($urls_array[$url])) {
                    unset($this->urls[$type][$url]);
                }
            }
        }       

        return $this;
    }

    /**
     * Preloads an url
     * @param string|array $url The url(s) to preload
     * @return static
     */
    public function preload(string|array $url) : static
    {
        return $this->load($url, 'preload');
    }

    /**
     * Prefetches an url
     * @param string|array $url The url(s) to prefetch
     * @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
     * @return static
     */
    public function prefetch(string|array $url, bool|string $version = true) : static
    {
        return $this->load($url, 'prefetch');
    }
   
    /**
     * Returns the list of urls
     * @param string $type The type of the url [head|footer]
     * @param bool $sort If true, will sort the urls by priority
     * @return array
     */
    public function get(string $type, bool $sort = true) : array
    {
        $urls = $this->urls[$type] ?? [];

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
     * @param string $type The type of the url [head|footer]
     */
    public function output(string $type)
    {
        $urls = $this->get($type);

        foreach ($urls as $url => $data) {
            $this->outputUrl($data['url'], $data['attributes']);
        }
    }
}
