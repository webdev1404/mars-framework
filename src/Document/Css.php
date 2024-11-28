<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Document's Css Urls Class
 * Class containing the css urls/stylesheets used by a document
 */
class Css extends Urls
{
    /**
     * @see \Mars\Document\Urls::$version
     * {@inheritdoc}
     */
    protected string $version {
        get => $this->app->config->css_version;
    }

    /**
     * @see \Mars\Document\Urls::outputUrl()
     * {@inheritdoc}
     */
    public function outputUrl(string $url, bool $async = false, bool $defer = false)
    {
        echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url) . '" />' . "\n";

        return $this;
    }
}
