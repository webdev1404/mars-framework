<?php
/**
* The Base Class for the theme document elements
* @package Mars
*/

namespace Mars\Extensions\Themes\Links;

use Mars\Url;
use Mars\Document\Links\Links as DocumentLinks;

/**
 * The Base Class for the theme document elements
 */
abstract class Links extends Base
{
    /**
     * @var DocumentLinks $urls The urls object
     */
    public DocumentLinks $urls;

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

            $this->assets_url = $this->theme->assets_url . '/' . rawurlencode($this->assets_dir);

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
        if ($this->app->url->isValid($url)) {
            return $url;
        }

        return $this->assets_url . '/' . $url;
    }

    /**
     * Returns the urls, with the assets url prepended
     * @param string|array $urls The urls to prepend the assets url to
     * @return array The urls
     */
    protected function getUrls(string|array $urls) : array
    {
        $urls = (array)$urls;

        return array_map(function ($url) {
            return $this->getUrl($url);
        }, $urls);
    }

    /**
     * Renders a link
     * @param string $url The url to render
     * @param array $attributes The attributes of the url, if any
     */
    public function renderLink(string $url, array $attributes = [])
    {
        $url = $this->assets_url . '/' . $url;

        $this->urls->renderLink($url, $attributes);
    }

    /**
     * Renders the given code
     * @param string $code The code to render
     */
    public function renderCode(string $code)
    {
        $this->urls->renderCode($code);
    }

    /**
     * Adds the given urls to the theme links
     * @param string|array $urls The urls to add
     * @param string $location The location to add the urls to
     * @param int $priority The priority of the urls
     * @param array $attributes The attributes of the urls
     * @param bool $early_hints Whether to use early hints
     * @param bool $preload Whether to preload the urls
     */
    public function add(string|array $urls, string $location = 'head', int $priority = 100, array $attributes = [], bool $early_hints = false, bool $preload = false) : static
    {
        $this->urls->add($this->getUrls($urls), $location, $priority, $attributes, $early_hints, $preload);

        return $this;
    }

    /**
     * Removes the given urls from the theme links
     * @param string|array $urls The urls to remove
     */
    public function remove(string|array $urls) : static
    {
        $this->urls->remove($this->getUrls($urls));

        return $this;
    }
}
