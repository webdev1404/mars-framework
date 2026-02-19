<?php
/**
* The Asset Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\Url;
use Mars\App\Kernel;
use Mars\Cache\Assets\Lists\Assets as CacheList;
use Mars\Cache\Assets\Urls\Asset as CacheUrl;
use Mars\Document\Links\Urls as DocumentUrls;

/**
 * The Asset Class
 * Minifies & combines content
 */
abstract class Asset
{
    use Kernel;

    /**
     * @var CacheList $cache_list The list cache object
     */
    protected CacheList $cache_list;

    /**
     * @var CacheUrl $cache_url The url cache object
     */
    protected CacheUrl $cache_url;

    /**
     * @var DocumentUrls $urls The urls object
     */
    protected DocumentUrls $urls;

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
     * @param array $urls The urls to process
     * @param bool $minify If true, will minify the assets
     * @param array $minify_exclude The urls to exclude from minification
     * @param bool $combine If true, will combine the assets
     * @param array $combine_exclude The urls to exclude from combination
     * @return array The processed urls
     */
    public function process(array $urls, bool $minify, array $minify_exclude, bool $combine, array $combine_exclude) : array
    {
        $cache_key = json_encode(['urls' => $urls, 'minify' => $minify, 'combine' => $combine]);
        $urls_list = $this->cache_list->get($cache_key);
        if ($this->development) {
            $urls_list = null;
        }
        if ($urls_list !== null) {
            return $urls_list;
        }

        $urls_list = array_filter($urls, fn ($url) => !$url['is_local']);
        $local_urls = array_filter($urls, fn ($url) => $url['is_local']);
        if (!$local_urls) {
            return $urls_list;
        }

        $processed_urls = $this->prepare($local_urls);
        if ($minify) {
            $processed_urls = $this->minify($processed_urls, $minify_exclude);
        }
        if ($combine) {
            $processed_urls = $this->combine($processed_urls, $combine_exclude);
        }

        $urls_list = array_merge($urls_list, $processed_urls);

        $this->cache_list->set($cache_key, $urls_list);

        return $urls_list;
    }

    /**
     * Prepares the given urls for processing
     * @param array $urls_list The urls to prepare
     * @return array The prepared urls
     * @throws \Exception If an asset file is not found
     */
    protected function prepare(array $urls_list) : array
    {
        foreach ($urls_list as &$url) {
            $file = new Url($url['url'])->getLocalFile($this->app->assets_path, true);
            if (!$file) {
                throw new \Exception('Asset file not found for url: ' . $url['url'] . ' in path: ' . $this->app->assets_path);
            }

            $url['filename'] = $file->filename;
            $url['original_url'] = $url['url'];
        }

        return $urls_list;
    }

    /**
     * Minifies the given urls
     * @param array $urls_list The urls to process
     * @param array $minify_exclude The urls to exclude from minification
     * @return array The processed urls
     */
    protected function minify(array $urls_list, array $minify_exclude) : array
    {
        $urls = [];
        foreach ($urls_list as $url) {
            if ($this->canMinify($url['original_url'], $minify_exclude)) {
                $this->cache_url->set($url['url'], $this->minifyContent($this->app->file->read($url['filename'])));

                $name = $this->cache_url->getName($url['url']);

                $url['filename'] = $this->cache_url->path . '/' . $name;
                $url['url'] = $this->urls->getUrl($this->cache_url->url . '/' . $name, true);
            }
        
            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * Checks whether the given url can be minified
     * @param string $url The url to check
     * @param array $minify_exclude The urls to exclude from minification
     * @return bool True if can be minified, false otherwise
     */
    protected function canMinify(string $url, array $minify_exclude) : bool
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
     * @param string $url The url to check
     * @param string $exclude The exclude pattern
     * @return bool True if matches, false otherwise
     */
    protected function matches(string $url, string $exclude) : bool
    {
        if (str_contains($exclude, '*')) {
            $pattern = str_replace('\*', '.*', preg_quote($exclude, '/'));

            return preg_match('/' . $pattern . '/', $url);
        }

        return str_contains($url, $exclude);
    }

    /**
     * Combines the given urls
     * @param array $urls_list The urls to process
     * @param array $combine_exclude The urls to exclude from combination
     * @return array The processed urls
     */
    protected function combine(array $urls_list, array $combine_exclude) : array
    {
        $urls = [];
        $combined_urls = [];
        $content = '';

        foreach ($urls_list as $url) {
            if (!$this->canCombine($url['original_url'], $combine_exclude)) {
                $urls[] = $url;
                continue;
            }

            $combined_urls[] = $url;
            $content .= $this->app->file->read($url['filename']) . "\n";
        }

        if ($combined_urls) {
            $combined_key = json_encode($combined_urls);

            $this->cache_url->set($combined_key, $content);
            
            $urls[] = [
                'url' => $this->urls->getUrl($this->cache_url->url . '/' . $this->cache_url->getName($combined_key), true),
                'is_local' => true,
                'attributes' => []
            ];
        }

        return $urls;
    }

    /**
     * Checks whether the given url can be combined
     * @param string $url The url to check
     * @param array $combine_exclude The urls to exclude from combination
     * @return bool True if can be combined, false otherwise
     */
    protected function canCombine(string $url, array $combine_exclude) : bool
    {
        if (!$combine_exclude) {
            return true;
        }

        foreach ($combine_exclude as $exclude) {
            if ($this->matches($url, $exclude)) {
                return false;
            }
        }

        return true;
    }
}
