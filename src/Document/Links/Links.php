<?php
/**
* The Links Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Document\Url;
use Mars\Document\Urls;

/**
 * The Document Links Class
 * Abstract class containing the urls & their corresponding locations used by a document
 */
abstract class Links
{
    use Kernel;
    use LazyLoad;

    /**
     * @var string $type The type of the url
     */
    public protected(set) string $type = '';

    /**
     * @var string $version The version to be applied to the urls
     */
    public string $version = '';

    /**
     * @var array $urls Array with all the urls to be rendered
     */
    public protected(set) array $urls = [];

    /**
     * @var array $codes Array with all the codes to be rendered
     */
    public protected(set) array $codes = [];

    /**
     * @var bool $minify Indicates whether the urls should be minified
     */
    protected bool $minify = false;

    /**
     * @var array $minify_exclude The urls to be excluded from minification
     */
    protected array $minify_exclude = [];

    /**
     * @var bool $development If true, we are in development mode
     */
    protected bool $development {
        get => $this->app->config->development->enable;
    }

    /**
     * Renders a link
     * @param Url $url The url to render
     */
    abstract public function renderLink(Url $url);

    /**
     * Renders the given code
     * @param string $code The code to render
     */
    abstract public function renderCode(string $code);

    /**
     * Builds the Links object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($this->app);
    }

    /**
     * Returns the version to be applied to the urls
     * @param string $cache_file The cache file name where the version is stored
     * @return string The version
     */
    protected function getVersion(string $cache_file) : string
    {
        $version = $this->app->cache->data->get($cache_file);
        if ($version === null) {
            $version = time();
            $this->app->cache->data->set($cache_file, $version);
        }

        return $version;
    }

    /**
     * Returns the url with the version applied if it's local
     * @param string $url The url to process
     * @param bool|null $is_local If null, will automatically detect if the url is local or not
     * @return string The url
     */
    public function getUrl(string $url, ?bool $is_local = null) : string
    {
        if ($is_local === null) {
            $is_local = $this->app->url->isLocal($url);
        }

        if (!$is_local) {
            return $url;
        }

        return $this->app->url->add($url, ['ver' => $this->version]);
    }

    /**
     * Adds code to be rendered
     * @param string $code The code to add
     * @param string $location The location of the code [head|footer]
     * @return static
     */
    public function addCode(string $code, string $location = 'head') : static
    {
        $this->codes[$location][] = $code;

        return $this;
    }

    /**
     * Adds the url on the list of urls to be rendered
     * @param string|array $urls The url(s) to add. Will only add it once, no matter how many times the function is called with the same url
     * @param string $location The location of the url [head|footer]
     * @param int $priority The url's priority (higher number means higher priority)
     * @param array $attributes The attributes of the url, if any
     * @param bool $early_hints If true, will add the url as an early hint
     * @param bool $preload If true, will add the url as a preload
     * @return static
     */
    public function add(string|array $urls, string $location = 'head', int $priority = 100, array $attributes = [], bool $early_hints = false, bool $preload = false) : static
    {
        $urls = (array)$urls;

        $this->urls[$location] ??= new Urls($this->type, $this->app);

        foreach ($urls as $url) {
            $url = new Url($url, $this->type, $attributes, $priority, $this->app);

            $this->urls[$location]->add($url);

            if ($early_hints) {
                $this->app->response->headers->early_hints->preload->add($this->type, $url);
            }

            if ($preload) {
                $this->app->document->preload->add($this->type, $url);
            }
        }

        return $this;
    }

    /**
     * Removes a url/urls from the list of urls to be rendered
     * @param string|array $urls The url(s) to remove
     * @return static
     */
    public function remove(string|array $urls) : static
    {
        $urls = (array)$urls;

        foreach ($urls as $url) {
            $this->app->response->headers->early_hints->preload->remove($url);
            $this->app->document->preload->remove($url);

            foreach ($this->urls as $location => $urls_list) {
                $urls_list->remove($url);
            }
        }

        return $this;
    }

    /**
     * Returns the list of urls
     * @param string $location The location of the url [head|footer]
     * @return Urls|null The list of urls
     */
    public function get(string $location) : ?Urls
    {
        $urls = $this->urls[$location] ?? null;
        if (!$urls) {
            return null;
        }

        $urls->sort();

        if ($this->development || !$this->minify|| !isset($this->asset)) {
            return $urls;
        }

        return $this->asset->process($urls, $this->minify, $this->minify_exclude);
    }

    /**
     * Renders the urls
     * @param string $location The location of the url [head|footer]
     */
    public function render(string $location)
    {
        $urls = $this->get($location);
        if (!$urls) {
            return;
        }

        $this->app->plugins->run('document.links.render', $urls, $location, $this);

        foreach ($urls as $url) {
            $this->renderLink($url);
        }
    }

    /**
     * Renders the codes
     * @param string $location The location of the code [head|footer]
     */
    public function renderCodes(string $location)
    {
        $codes = $this->codes[$location] ?? [];
        if (!$codes) {
            return;
        }

        foreach ($codes as $code) {
            $this->renderCode($code);
        }
    }

    /**
     * Returns the nonce code
     * @return string
     */
    public function getNonce() : string
    {
        //if nonce is not enabled or page caching or accelerator is enabled, return empty string
        if (!$this->app->config->headers->csp->enable || !$this->app->config->headers->csp->use_nonce) {
            return '';
        }
        if (!$this->app->response->headers->csp->canUseNonce()) {
            return '';
        }

        return ' nonce="' . $this->app->nonce . '"';
    }
}
