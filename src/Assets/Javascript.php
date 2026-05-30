<?php
/**
* The Javascript Asset Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\Cache\Assets\Lists\Assets as CacheList;
use Mars\Cache\Assets\Urls\Asset as CacheUrl;

/**
 * The Javascript Asset Class
 * Processes javascript assets by minifying them
 */
class Javascript extends Asset
{
    /**
     * @see CacheList::$list
     * {@inheritDoc}
     */
    protected CacheList $cache_list {
        get => $this->app->cache->js_list;
    }

    /**
     * @see CacheUrl::$cache_url
     * {@inheritDoc}
     */
    protected CacheUrl $cache_url {
        get => $this->app->cache->js;
    }

    /**
     * @see Asset::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'script';

    /**
     * @see Asset::$dir
     * {@inheritDoc}
     */
    protected string $dir = 'js';

    /**
     * @see Asset::minify()
     * {@inheritDoc}
     */
    protected function minifyContent(string $content) : string
    {
        $minifier = new Minifier($this->app);

        return $minifier->minifyJs($content);
    }
}
