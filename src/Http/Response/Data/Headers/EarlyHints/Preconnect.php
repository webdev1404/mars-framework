<?php
/**
* The Early Hints Preconnect Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers\EarlyHints;

use Mars\App;
use Mars\Data\ListTrait;

/**
 * The Early Hints Preconnect Class
 * Contains the early hints preconnect headers
 */
class Preconnect extends Base
{
    use ListTrait;

    /**
     * Builds the Preconnect object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->urls = $this->app->config->headers->early_hints->list['preconnect'] ?? [];
    }

    /**
     * Returns the headers to send as early hints
     * @return array The headers
     */
    public function getHeaders() : array
    {
        $headers = [];

        $chunks = array_chunk($this->getUrls($this->urls), $this->links_per_header);

        foreach ($chunks as $urls) {
            $links = array_map(function($url) {
                $crossorigin = $url['crossorigin'] ? '; crossorigin' : '';
                return "<{$url['url']}>; rel=preconnect{$crossorigin}";
            }, $urls);

            $headers[] = 'Link: ' . implode(', ', $links);
        }

        return $headers;
    }

    /**
     * Returns the list of URLs to send as early hints
     * @return array The list of URLs 
     */
    public function getAll() : array
    {
        return $this->getUrls($this->urls);
    }
}
