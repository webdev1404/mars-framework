<?php
/**
* The Preload Urls Class
* @package Mars
*/

namespace Mars\Document\Hints;

use Mars\Document\Url;
use Mars\Document\UrlsGroup;

/**
 * The Preload Urls Class
 * Class containing the preload functionality used by a document
 */
class Preload extends UrlsGroup
{
    /**
     * Renders the preload urls
     */
    public function render()
    {
        $this->addMany($this->app->config->document->hints->preload);

        foreach ($this->urls as $type => $urls_array) {
            foreach ($urls_array as $url) {
                $this->renderLink($url);
            }
        }

        //reset the urls to save some memory
        $this->urls = [];
    }

    /**
     * Returns the attributes to add to the link
     * @param Url $url The url to get the attributes from
     * @return string The attributes to add to the link
     */
    protected function getAttributes(Url $url) : string
    {
        $allowed_attributes = ['crossorigin', 'integrity'];

        $attributes = array_filter($url->attributes, fn($attribute) => in_array($attribute, $allowed_attributes), ARRAY_FILTER_USE_KEY);

        return $this->app->html->getAttributes($attributes);
    }

    /**
     * Renders the given link
     * @param Url $url The url to render
     */
    public function renderLink(Url $url)
    {
        echo '<link rel="preload" href="' . $this->app->escape->html($url->url) . '" as="' . $url->type . '"' . $this->getAttributes($url) . ' />' . "\n";
    }
}
