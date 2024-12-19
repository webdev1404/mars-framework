<?php
/**
* The Content Security Policy Header Response Class
* @package Mars
*/

namespace Mars\Response\Headers;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Content Security Policy Header Response Class
 * Handles the Content Security Policy
 */
class CSP
{
    use InstanceTrait;

    /**
     * @var array $document_assets The default document sources to get the urls from
     */
    protected array $document_assets = [
        'style-src' => 'css',
        'script-src' => 'javascript',
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
        if (!$this->app->config->headers_csp_enable) {
            return;
        }

        //get the sources from the defaults
        foreach ($this->app->config->headers_csp_defaults as $name => $src) {
            $this->results[$name] = $src;
        }

        //get the sources from the document assets
        foreach ($this->document_assets as $name => $source) {
            $this->results[$name] = $this->getFromDocument($name, $source);
        }

        //get the sources from the config
        if ($this->app->config->headers_csp) {
            foreach ($this->app->config->headers_csp as $name => $src) {
                if ($src) {
                    $this->results[$name] = $src;
                }
            }
        }

        //add noonce
        if ($this->app->config->headers_csp_use_nonce) {
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
        if (isset($this->app->config->headers_csp[$name])) {
            return $this->app->config->headers_csp[$name];
        }

        $external_urls = [];
        $urls = $this->app->document->$source->urls;
        foreach ($urls as $type => $urls_array) {
            foreach ($urls_array as $url) {
                if ($url['is_local']) {
                    continue;
                }

                $external_urls[] = $this->app->uri->getRoot($url['url']);
            }           
        }

        $default = $this->defaults[$name] ?? '';
        if (!$external_urls) {
            return $default;
        }

        $external_urls = array_unique($external_urls);
        $external_urls = array_filter($external_urls);

        return $default . ' ' . implode(' ', $external_urls);
    }
}
