<?php
/**
* The Fonts Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Fonts Urls Class
 * Class containing the fonts functionality used by a document
 */
class Fonts extends Urls
{
    /**
     * @see \Mars\Document\Urls::outputPreloadUrl()
     * {@inheritdoc}
     */
    public function outputPreloadUrl(string $url)
    {
        echo '<link rel="preload" href="' . $this->app->escape->html($url) . '" as="font" crossorigin="anonymous" />' . "\n";
    }

    /**
     * Does nothing
     */
    public function outputUrl(string $url, array $attributes = [])
    {
    }
}
