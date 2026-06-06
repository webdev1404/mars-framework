<?php
/**
* The Early Hints Preload Class
* @package Mars
*/

namespace Mars\Http\Response\Headers\EarlyHints;

use Mars\App;
use Mars\Document\Url;
use Mars\Document\UrlsGroup;

/**
 * The Early Hints Preload Class
 * Contains the early hints preload headers
 */
class Preload extends UrlsGroup
{
    use BaseTrait;

    /**
     * Returns the links to send as early hints
     * @return array The links to send as early hints
     */
    public function getLinks() : array
    {
        $links = [];
        $urls = $this->getUrls();

        foreach ($urls as $type => $urls_list) {
            if (!$urls_list->count()) {
                continue;
            }

            foreach ($urls_list as $url) {
                $links[] = $this->getLink($url);
            }
        }

        return $links;
    }

    /**
     * Returns the list of URLs to send as early hints
     * @return array The list of URLs
     */
    protected function getUrls() : array
    {
        $this->addMany($this->app->config->headers->early_hints->list['preload'] ?? []);
        
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

        return "<{$url->url}>; rel=preload; as={$url->type}{$crossorigin}";
    }
}
