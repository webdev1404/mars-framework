<?php
/**
* The RSS tag Class
* @package Mars
*/

namespace Mars\Document\Tags;

/**
 * The Document's RSS tag Class
 * Stores the <link rel="alternate" type="application/rss+xml"> tags of the document
 */
class Rss extends Tags
{
    /**
     * Outputs a RSS tag
     * @param string $url The url of the RSS file.
     * @param string $title The title of the feed
     * @return static
     */
    public function outputTag(string $url, string $title) : static
    {
        echo '<link rel="alternate" type="application/rss+xml" title="' . $this->app->escape->html($title) . '" href="' . $this->app->escape->html($url) . '">' . "\n";

        return $this;
    }

    /**
     * Loads a RSS url
     * @param string $url The url of the RSS file.
     * @param string $title The title of the feed
     * @return static
     */
    public function load(string $url, string $title) : static
    {
        return $this->add($url, $title);
    }

    /**
     * Unloads a RSS url
     * @param string $url The url of the RSS file.
     * @return static
     */
    public function unload(string $url) : static
    {
        return $this->remove($url);
    }
}
