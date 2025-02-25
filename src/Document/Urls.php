<?php
/**
* The Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Lazyload;
use Mars\Lazyload\GhostTrait;
use Mars\Document\Urls\Preload;
use Mars\Document\Urls\Prefetch;

/**
 * The Document Urls Class
 * Abstract class containing the urls & their corresponding locations used by a document
 */
abstract class Urls
{
    use InstanceTrait;
    use GhostTrait;

    /**
     * @var string $version The version to be applied to the urls
     */
    public protected(set) string $version = '';

    /**
     * @var string $type The type of the preload
     */
    public protected(set) string $type = '';

    /**
     * @var string $preload_config_key The config key which holds the preload urls
     */
    public protected(set) string $preload_config_key = '';

    /**
     * @var string $crossorigin The crossorigin attribute of the url
     */
    public protected(set) string $crossorigin = '';

    /**
     * @var array $urls Array with all the urls to be outputed
     */
    public protected(set) array $urls = [];

    /**
     * @var Preload $preload The preload object
     */
    #[Lazyload]
    public protected(set) Preload $preload;

    /**
     * @var Prefetch $prefetch The prefetch object
     */
    #[Lazyload]
    public protected(set) Prefetch $prefetch;

    /**
     * Outputs an url
     * @param string $url The url to output
     * @param array $attributes The attributes of the url, if any
     */
    abstract public function outputUrl(string $url, array $attributes = []);

    /**
     * Builds the Urls object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;

        if ($this->preload_config_key) {
            $urls = $this->app->config->preload[$this->preload_config_key] ?? [];
            if ($urls) {
                $this->preload($urls);
            }
        }
    }

    /**
     * Loads an url
     * @param string|array $urls The url(s) to load. Will only load it once, no matter how many times the function is called with the same url
     * @param string $type The type of the url [head|footer]
     * @param int $priority The url's output priority. The higher, the better
     * @param bool $preload If true, will output the url as a preload
     * @param array $attributes The attributes of the url, if any
     * @return static
     */
    public function load(string|array $urls, string $type = 'head', int $priority = 100, bool $preload = false, array $attributes = []) : static
    {
        $urls = (array)$urls;

        foreach ($urls as $url) {
            $is_local = $this->app->uri->isLocal($url);
            $full_url = $this->getUrl($url, $is_local);

            if ($preload) {
                $this->preload($full_url, false);
            }

            $this->urls[$type][$url] = [
                'url' => $full_url,
                'priority' => $priority,
                'attributes' => $attributes, 
                'is_local' => $is_local,
            ];
        }

        return $this;
    }

    /**
     * Returns the url, with the version appended
     * @param string $url The url to append the version to
     * @param bool $is_local If true, will append the version only to local urls
     * @return string
     */
    protected function getUrl(string $url, ?bool $is_local = null) : string
    {
        if (!$this->version) {
            return $url;
        }
        if ($is_local === null) {
            $is_local = $this->app->uri->isLocal($url);
        }
        if (!$is_local) {
            return $url;
        }

        return $this->app->uri->build($url, ['ver' => $this->version]);
    }

    /**
     * Returns the urls, with the version appended
     * @param string|array $urls The url(s) to append the version to
     * @return array
     */
    protected function getUrls(string|array $urls) : array
    {
        $urls = (array)$urls;

        return array_map(function($url) {
            return $this->getUrl($url);
        }, $urls);
    }

    /**
     * Unloads an url/urls
     * @param string|array $urls The url(s) to unload
     * @return static
     */
    public function unload(string|array $urls) : static
    {
        $urls = (array)$urls;

        foreach ($urls as $url) {
            $this->unloadPreload($url);

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
     * @param string|array $urls The url(s) to preload
     * @param bool $version If true, will append the version to the url
     * @return static
     */
    public function preload(string|array $urls, bool $version = true) : static
    {        
        $urls = (array)$urls;

        if ($version) {
            $urls = $this->getUrls($urls);
        }

        $this->preload->load($urls, $this->type);

        return $this;
    }

    /**
     * Unloads the preloaded urls
     * @param string|array $urls The url(s) to unload
     * @return static
     */
    public function unloadPreload(string|array $urls) : static
    {
        $this->preload->unload($this->getUrls($urls));

        return $this;
    }

    /**
     * Prefetches an url
     * @param string|array $urls The url(s) to prefetch
     * @param bool $version If true, will append the version to the url
     * @return static
     */
    public function prefetch(string|array $urls, bool $version = true) : static
    {
        $urls = (array)$urls;

        if ($version) {
            $urls = $this->getUrls($urls);
        }

        $this->prefetch->load($urls);

        return $this;
    }

    /**
     * Unloads the prefetched urls
     * @param string|array $urls The url(s) to unload
     * @return static
     */
    public function unloadPrefetch(string|array $urls) : static
    {
        $this->prefetch->unload($this->getUrls($urls));

        return $this;
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

    /**
     * Returns the nonce code
     * @return string
     */
    public function getNonce() : string
    {
        if (!$this->app->config->csp_enable || !$this->app->config->csp_use_nonce) {
            return '';
            
        }

        return ' nonce="' . $this->app->nonce . '"';
    }
}
