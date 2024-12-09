<?php
/**
* The Images Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Images Urls Class
 * Class containing the images functionality used by a document
 */
class Images extends Urls
{
    /**
     * @see \Mars\Document\Urls::outputPreloadUrl()
     * {@inheritdoc}
     */
    public function outputPreloadUrl(string $url)
    {
        echo '<link rel="preload" href="' . $this->app->escape->html($url) . '" as="image" />' . "\n";
    }

    /**
     * Does nothing
     */
    public function outputUrl(string $url, array $attributes = [])
    {
    }
}
