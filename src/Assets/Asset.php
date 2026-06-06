<?php
/**
* The Asset Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\App\Kernel;
use Mars\Cache\Assets\Lists\Assets as CacheList;
use Mars\Cache\Assets\Urls\Asset as CacheUrl;
use Mars\Document\Url;
use Mars\Document\Urls;

/**
 * The Asset Class
 * Processes assets by minifying them
 */
abstract class Asset
{
    use Kernel;

    /**
     * @var CacheList $cache_list The cache object handling the caching of the list of asset URLs
     */
    protected CacheList $cache_list;

    /**
     * @var CacheUrl $cache_url The cache object handling the caching of a single asset URL
     */
    protected CacheUrl $cache_url;

    /**
     * @var string $type The asset type (e.g. 'script' or 'style')
     */
    public protected(set) string $type = '';

    /**
     * @var string $dir The assets directory
     */
    protected string $dir = '';

    /**
     * @var bool $development If true, we are in development mode
     */
    protected bool $development {
        get => $this->app->config->development->enable;
    }

    /**
     * Minifies the given content
     * @param string $content The content to minify
     * @return string The minified content
     */
    abstract protected function minifyContent(string $content) : string;

    /**
     * Processes the given urls by minifying and/or combining them
     * @param Urls $urls The urls to process
     * @param bool $minify If true, will minify the assets
     * @param array $minify_exclude The urls to exclude from minification
     * @return Urls The processed urls
     */
    public function process(Urls $urls, bool $minify, array $minify_exclude) : Urls
    {
        if (!$minify) {
            return $urls;
        }

        $cache_key = json_encode(['urls' => $urls->urls, 'minify' => $minify]);
        $urls_list = $this->cache_list->get($cache_key);

        if ($this->development) {
            $urls_list = null;
        }

        if ($urls_list !== null) {
            return $urls_list;
        }
  
        //minify only local urls
        $local_urls = $urls->getLocal();
        if (count($local_urls)) {
            $urls_list = $urls->getExternal();

            $urls_list->add($this->minify($local_urls, $minify_exclude));
        }

        $this->app->plugins->run('assets.processed', $urls_list, $urls, $local_urls, $external_list, $minify_exclude);

        $this->cache_list->set($cache_key, $urls_list);

        return $urls_list;
    }

    /**
     * Minifies the given urls
     * @param array $urls The urls to minify
     * @param array $minify_exclude The urls to exclude from minification
     * @return Urls The processed urls
     */
    protected function minify(Urls $urls, array $minify_exclude) : Urls
    {
        $minified_urls = new Urls($this->type, $this->app);

        foreach ($urls as $url) {
            if (!$url->filename) {
                throw new \Exception("Could not minify url {$url->url} because it does not have a filename");
            }
            if ($this->canMinify($url, $minify_exclude)) {
                $this->cache_url->set($url->url, $this->minifyContent($this->app->file->read($url->filename)));

                $url = new Url($url->type, $this->cache_url->base_url . '/' . $this->cache_url->getName($url->url), $url->attributes, $url->priority, $this->app);
            }

            $minified_urls->add($url);
        }

        return $minified_urls;
    }

    /**
     * Checks whether the given url can be minified
     * @param Url $url The url to check
     * @param array $minify_exclude The urls to exclude from minification
     * @return bool True if can be minified, false otherwise
     */
    protected function canMinify(Url $url, array $minify_exclude) : bool
    {
        if (!$minify_exclude) {
            return true;
        }

        foreach ($minify_exclude as $exclude) {
            if ($this->matches($url, $exclude)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the given url matches the exclude pattern
     * @param Url $url The url to check
     * @param string $exclude The exclude pattern
     * @return bool True if matches, false otherwise
     */
    protected function matches(Url $url, string $exclude) : bool
    {
        if (str_contains($exclude, '*')) {
            $pattern = str_replace('\*', '.*', preg_quote($exclude, '/'));

            return preg_match('/' . $pattern . '/', $url->url);
        }

        return str_contains($url->url, $exclude);
    }
}
