<?php
/**
* The Early Hints Preload Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers\EarlyHints;

use Mars\App;
use Mars\Document\UrlsGroup;

/**
 * The Early Hints Preload Class
 * Contains the early hints preload headers
 */
class Preload extends UrlsGroup
{
    /**
     * Returns the list of URLs to send as early hints
     * @return array The list of URLs
     */
    protected function getUrls() : array
    {
        $links = 

        //$config_urls = $this->app->config->headers->early_hints->list['preload'] ?? [];
        //foreach 

        //$links = new Links($this->app);
        $links->addMany($this->app->config->headers->early_hints->list['preload'] ?? []);
var_dump($links);die;
        //get the urls from the config
        $config_urls = $this->app->config->headers->early_hints->list['preload'] ?? [];
        foreach ($config_urls as $type => $urls_list) {
            $urls[$type] = $this->app->document->getVersionedUrls($type, $urls_list);
        }

        //add the added urls
        $urls = array_merge($urls, $this->urls);

        return $urls;
    }

    /**
     * Returns the headers to send as early hints
     * @return array The headers
     */
    public function getHeaders() : array
    {
        $headers = [];

        $urls = $this->getUrls();
        var_dump($urls);die;
        foreach ($this->urls as $type => $urls_list) {
            if (!$urls_list) {
                continue;
            }

            $chunks = array_chunk($this->getUrls($urls_list), $this->links_per_header);

            foreach ($chunks as $urls) {
                $links = array_map(function($url) use ($type) {
                    $crossorigin = $url['crossorigin'] ? '; crossorigin' : '';
                    return "<{$url['url']}>; rel=preload; as={$type}{$crossorigin}";
                }, $urls);

                $headers[] = 'Link: ' . implode(', ', $links);
            }
        }

        return $headers;
    }
}
