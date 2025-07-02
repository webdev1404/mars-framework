<?php
/**
* The Base Class for the theme document elements
* @package Mars
*/

namespace Mars\Extensions\Themes\Links;

use Mars\App;
use Mars\App\Kernel;
use Mars\Document\Links\Urls;

/**
 * The Base Class for the theme document elements
 */
abstract class Url
{
    use Kernel;

    /**
     * @var Urls $url The urls object
     */
    protected Urls $url;

    /**
     * @var string $assets_dir The assets directory where the theme assets are stored
     */
    protected string $assets_dir = '';

    /**
     * @var string $assets_url The url pointing to the folder where the assets for the extension are located
     */
    protected string $assets_url {
        get {
            if (isset($this->assets_url)) {
                return $this->assets_url;
            }

            $this->assets_url = $this->app->theme->assets_url . '/' . rawurlencode($this->assets_dir);

            return $this->assets_url;
        }
    }

    /**
     * Returns the url of the assets
     * @param string $url The url to return
     * @return string The url
     */
    public function getUrl(string $url) : string
    {
        $url = $this->assets_url . '/' . $url;

        return $this->url->getUrl($url);
    }

    /**
     * Outputs a link
     * @param string $url The url to output
     * @param array $attributes The attributes of the url, if any
     */
    public function outputLink(string $url, array $attributes = [])
    {
        $url = $this->assets_url . '/' . $url;

        $this->url->outputLink($url, $attributes);
    }

    /**
     * Outputs the given code
     * @param string $code The code to output
     */
    public function outputCode(string $code)
    {
        $this->url->outputCode($code);
    }

    /**
     * @see Urls::load()
     * {@inheritdoc}
     */
    public function load(string|array $urls, string $type = 'head', int $priority = 100, bool $preload = false, array $attributes = []) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->load($urls, $type, $priority, $preload, $attributes);

        return $this;
    }

    /**
     * @see Urls::unload()
     * {@inheritdoc}
     */
    public function unload(string|array $urls) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->unload($urls);

        return $this;
    }

    /**
     * @see Urls::preload()
     * {@inheritdoc}
     */
    public function preload(string|array $urls, bool $add_version = false) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->preload($urls, $add_version);

        return $this;
    }

    /**
     * @see Urls::unloadPreload()
     * {@inheritdoc}
     */
    public function unloadPreload(string|array $urls) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->unloadPreload($urls);

        return $this;
    }

    /**
     * @see Urls::prefetch()
     * {@inheritdoc}
     */
    public function prefetch(string|array $urls, bool $add_version = true) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->prefetch($urls, $add_version);

        return $this;
    }

    /**
     * @see Urls::unloadPrefetch()
     * {@inheritdoc}
     */
    public function unloadPrefetch(string|array $urls) : static
    {
        $urls = $this->getUrls($urls);

        $this->url->unloadPrefetch($urls);

        return $this;
    }

    /**
     * Returns the urls, with the assets url prepended
     * @param string|array $urls The urls to prepend the assets url to
     * @return array The urls
     */
    protected function getUrls(string|array $urls) : array
    {
        $urls = (array)$urls;

        return array_map(function($url) {
            return $this->assets_url . '/' . $url;
        }, $urls);
    }
}