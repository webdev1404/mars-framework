<?php
/**
* The Content Security Policy Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers;

use Mars\App;
use Mars\Url;
use Mars\App\Kernel;

/**
 * The Content Security Policy Header Response Class
 * Handles the Content Security Policy
 */
class CSP
{
    use Kernel;

    /**
     * @var array $document_assets The default document sources to get the urls from
     */
    protected array $document_assets = [
        'style-src' => 'css',
        'script-src' => 'js',
        'font-src' => 'fonts',
        'img-src' => 'images',
    ];

    /**
     * @var array $results The CSP results
     */
    protected array $results = [];

    /**
     * Sends the CSP header
     */
    public function output()
    {
        if (!$this->app->config->http->response->headers->csp->enable) {
            return;
        }

        //get the sources from the defaults
        foreach ($this->app->config->http->response->headers->csp->defaults as $name => $src) {
            $this->results[$name] = $src;
        }

        //get the sources from the document assets
        foreach ($this->document_assets as $name => $source) {
            $this->results[$name] = $this->getFromDocument($name, $source);
        }

        //get the sources from the config
        if ($this->app->config->http->response->headers->csp->list) {
            foreach ($this->app->config->http->response->headers->csp->list as $name => $src) {
                if ($src) {
                    $this->results[$name] = $src;
                }
            }
        }

        //add nonce
        if ($this->app->config->http->response->headers->csp->use_nonce) {
            $nonce = $this->app->nonce;

            $this->results['script-src'] .= " 'nonce-$nonce'";
            $this->results['style-src'] .= " 'nonce-$nonce'";
        }

        header('Content-Security-Policy: ' . $this->getHeader());
    }

    /**
     * Returns the header's content
     * @return string
     */
    protected function getHeader() : string
    {
        $parts = [];
        foreach ($this->results as $name => $src) {
            if (!trim($src)) {
                continue;
            }

            $parts[] = $name . ' ' . trim($src);
        }

        return implode('; ', $parts);
    }

    /**
     * Returns the source from the document
     * @param string $name The name of the source
     * @param string $source The source
     * @return string
     */
    protected function getFromDocument(string $name, string $source) : string
    {
        //return from config if we have it set
        if (!empty($this->app->config->http->response->headers->csp->list[$name])) {
            return $this->app->config->http->response->headers->csp->list[$name];
        }

        $external_urls = [];
        $urls = $this->app->document->$source->urls;
        foreach ($urls as $type => $urls_array) {
            foreach ($urls_array as $url) {
                if ($url['is_local']) {
                    continue;
                }

                $external_urls[] = $this->app->url->getRoot($url['url']);
            }
        }

        $default = $this->app->config->http->response->headers->csp->defaults[$name] ?? '';
        if (!$external_urls) {
            return $default;
        }

        $external_urls = array_unique($external_urls);
        $external_urls = array_filter($external_urls);

        return $default . ' ' . implode(' ', $external_urls);
    }
}
