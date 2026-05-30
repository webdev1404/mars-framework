<?php
/**
* The Content Security Policy Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers;

use Mars\App;
use Mars\App\Kernel;
use Mars\Url;
use Mars\Data\ListGroupTrait;

/**
 * The Content Security Policy Header Response Class
 * Handles the Content Security Policy
 */
class CSP
{
    use Kernel;
    use ListGroupTrait;

    /**
     * @var array $urls The list of URLs to send
     */
    protected array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * @var array $assets_directives The default document assets directives to get the urls from
     */
    protected array $assets_directives = [
        'style-src' => 'css',
        'script-src' => 'js',
        'font-src' => 'fonts',
        'img-src' => 'images',
    ];

    /**
     * Determines whether a nonce can be used, based on the configuration and the cache/accelerator status
     * @return bool
     */
    public function canUseNonce() : bool
    {
        if ($this->app->config->cache->page->enable || $this->app->config->accelerator->enable) {
            return false;
        }

        return true;
    }

    /**
     * Prepares the CSP header
     */
    public function collect()
    {
        if (!$this->app->config->headers->csp->enable) {
            return;
        }

        if ($this->app->config->headers->csp->use_nonce) {
            if ($this->app->config->cache->page->enable || $this->app->config->accelerator->enable) {
                $this->app->config->headers->csp->use_nonce = false;
                $this->app->config->headers->csp->unsafe_inline = true;

            } else {
                //set unsafe-inline to false if using nonce, iregardless of the config value
                $this->app->config->headers->csp->unsafe_inline = false;
            }
        }

        $this->app->response->headers->add('Content-Security-Policy', $this->getHeader($this->getDirectives()));
    }

    /**
     * Returns the CSP directives
     * @return array The directives
     */
    protected function getDirectives() : array
    {
        $directives = [];

        //get the directives from the defaults
        foreach ($this->app->config->headers->csp->defaults as $name => $src) {
            $directives[$name][] = $src;
        }

        if ($this->app->config->headers->csp->unsafe_inline) {
            $directives['script-src'][] = "'unsafe-inline'";
            $directives['style-src'][] = "'unsafe-inline'";
        }

        //get the directives from the document assets
        foreach ($this->assets_directives as $name => $source) {
            if (!empty($this->app->config->headers->csp->list[$name])) {
                continue;
            }

            $directives[$name] = array_merge($directives[$name] ?? [], $this->getFromAsset($name, $source));
        }

        //get the added directives
        foreach ($this->urls as $name => $directives_array) {
            if (!empty($this->app->config->headers->csp->list[$name])) {
                continue;
            }

            $directives[$name] = array_merge($directives[$name] ?? [], $directives_array);
        }

        $defaults = $this->app->config->headers->csp->defaults['default-src'] ?? '';
        foreach ($directives as $name => $list) {
            //if the default-src is not already included in the directive, add it to the beginning of the list
            if (!isset($this->app->config->headers->csp->defaults[$name])) {
                array_unshift($list, $defaults);
            }

            $list = $list |> array_filter(...) |> array_unique(...);

            $directives[$name] = implode(' ', $list);
        }

        //overwrite with the directives from the config, if specified
        if ($this->app->config->headers->csp->list) {
            foreach ($this->app->config->headers->csp->list as $name => $src) {
                if ($src) {
                    $directives[$name] = $src;
                }
            }
        }

        //add nonce
        if ($this->app->config->headers->csp->use_nonce) {
            $nonce = $this->app->nonce;

            $directives['script-src'] .= " 'nonce-{$nonce}'";
            $directives['style-src'] .= " 'nonce-{$nonce}'";
        }

        return $directives;
    }

    /**
     * Returns the header's content
     * @param array $directives The directives to include in the header
     * @return string
     */
    protected function getHeader(array $directives) : string
    {
        $parts = [];
        foreach ($directives as $name => $src) {
            if (!trim($src)) {
                continue;
            }

            $parts[] = $name . ' ' . trim($src);
        }

        return implode('; ', $parts);
    }

    /**
     * Returns the directives from the document assets
     * @param string $name The name of the source
     * @param string $source The source
     * @return array
     */
    protected function getFromAsset(string $name, string $source) : array
    {
        $external_urls = [];

        $urls = $this->app->document->$source->urls;
        foreach ($urls as $type => $urls_array) {
            foreach ($urls_array as $url) {
                if ($url['is_local']) {
                    continue;
                }

                $external_urls[] = $this->app->url->getOrigin($url['url']);
            }
        }

        if (!$external_urls) {
            return [];
        }

        return $external_urls |> array_filter(...) |> array_unique(...);
    }
}
