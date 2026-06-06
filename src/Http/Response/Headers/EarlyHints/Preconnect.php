<?php
/**
* The Early Hints Preconnect Class
* @package Mars
*/

namespace Mars\Http\Response\Headers\EarlyHints;

use Mars\App;
use Mars\Document\Url;
use Mars\Document\Urls;

/**
 * The Early Hints Preconnect Class
 * Contains the early hints preconnect headers
 */
class Preconnect extends Urls
{
    use BaseTrait;

    /**
     * Builds the Preconnect object
     * @param App $app The app instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the links to send as early hints
     * @return array The links to send as early hints
     */
    public function getLinks() : array
    {
        $links = [];
        $urls = $this->getUrls();

        foreach ($urls as $url) {
            $links[] = $this->getLink($url);
        }

        return $links;
    }

    /**
     * Returns the list of URLs to send as early hints
     * @return array The list of URLs
     */
    protected function getUrls() : array
    {
        $this->addMany($this->app->config->headers->early_hints->list['preconnect'] ?? []);
        
        return $this->get();
    }

    /**
     * Returns the Link header for a given URL
     * @param Url $url The URL to get the header for
     * @return string The Link header
     */
    protected function getLink(Url $url) : string
    {
        $crossorigin = $url->attributes['crossorigin'] ?? false ? '; crossorigin' : '';

        return "<{$url->url}>; rel=preconnect{$crossorigin}";
    }
}
