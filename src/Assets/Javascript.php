<?php
/**
* The Javascript Asset Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\Cache\Assets\Lists\Assets as CacheList;
use Mars\Cache\Assets\Urls\Asset as CacheUrl;
use Mars\Document\Links\Urls as DocumentUrls;

/**
 * The Javascript Asset Class
 * Minifies & combines javascript content
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
     * @see DocumentUrls::$urls
     * {@inheritDoc}
     */
    protected DocumentUrls $urls {
        get => $this->app->document->js;
    }

    /**
     * @see Asset::$dir
     * {@inheritDoc}
     */
    protected string $dir = 'js';

    /**
     * @see Asset::$development
     * {@inheritDoc}
     */
    protected bool $development {
        get => $this->app->config->development->assets->process->css;
    }

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
