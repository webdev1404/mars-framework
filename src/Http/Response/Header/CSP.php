<?php
/**
* The Content Security Policy Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Header;

use Mars\App\LazyLoadProperty;
use Mars\Data\ListGroup;
use Mars\Data\ListGroupTrait;
use Mars\Http\Response\Header\CSP\Directives;

/**
 * The Content Security Policy Header Response Class
 * Handles the Content Security Policy
 */
class CSP extends Base
{
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
     * Registers the CSP header
     */
    public function register()
    {
        if (!$this->app->config->headers->csp->enable) {
            return;
        }

        //disable the use of nonce if the cache or accelerator is enabled, and set unsafe-inline to true in that case
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
     * Returns the directives to be sent
     * @return array The directives
     */
    protected function getDirectives() : array
    {
        $directives = new ListGroup($this->app);

        //add the default directives from the config
        $directives->addMany($this->app->config->headers->csp->defaults);

        if ($this->app->config->headers->csp->unsafe_inline) {
            $directives->addMany([
                'script-src' => "'unsafe-inline'",
                'style-src' => "'unsafe-inline'",
            ]);
        }

        //get the directives from the document assets
        foreach ($this->assets_directives as $name => $source) {
            if (!empty($this->app->config->headers->csp->list[$name])) {
                continue;
            }

            $urls = $this->getFromAsset($name, $source);
            if ($urls) {
                $directives->addMany($name, $this->getFromAsset($name, $source));
            }
        }

        //add the directives manually added
        $directives->addMany($this->urls);

        //overwrite with the directives from the config, if specified
        if ($this->app->config->headers->csp->list) {
            foreach ($this->app->config->headers->csp->list as $name => $src) {
                if ($src) {
                    $directives->set($name, [$src]);
                }
            }
        }

        //add nonce
        if ($this->app->config->headers->csp->use_nonce) {
            $nonce = $this->app->nonce;

            $directives->addMany([
                'script-src' => "'nonce-{$nonce}'",
                'style-src' => "'nonce-{$nonce}'",
            ]);
        }

        //if the default-src is not already included in the directive, add it to the beginning of the list
        //eg: 'self' will be added to all directives, if default-src has it
        $defaults = $this->app->config->headers->csp->defaults['default-src'] ?? [];
        foreach ($directives->get() as $name => $list) {
            if (isset($this->app->config->headers->csp->list[$name])) {
                continue;
            }

            if (!isset($this->app->config->headers->csp->defaults[$name])) {
                $directives->add($name, $defaults);
            }
        }

        return $directives->get();
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
            if (!$src) {
                continue;
            }

            $src = $src |> array_filter(...) |> array_unique(...);

            $parts[] = $name . ' ' . trim(implode(' ', $src));
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

        foreach ($this->app->document->$source->urls as $location => $urls_array) {
            $external_urls_array = $urls_array->getExternal($urls_array);

            foreach ($external_urls_array as $url) {
                $external_urls[] = $url->origin;
            }
        }

        return $external_urls;
    }
}
