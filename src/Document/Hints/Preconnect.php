<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document\Hints;

use Mars\App;
use Mars\Document\Url;
use Mars\Document\Urls;

/**
 * The Preconnect Urls Class
 * Class containing the preconnect functionality used by a document
 */
class Preconnect extends Urls
{
    /**
     * @var string $rel The rel attribute
     */
    protected string $rel = 'preconnect';

    /**
     * Builds the Preconnect object
     * @param App $app The app instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Outputs the preconnect urls
     */
    public function output()
    {
        $this->load();

        foreach ($this->urls as $url) {
            $this->outputLink($url);
        }

        //unset the urls to save some memory
        unset($this->urls);
    }

    /**
     * Loads the preconnect urls from the config
     */
    protected function load()
    {
        $this->addMany($this->app->config->hints->preconnect);
    }

    /**
     * Outputs a preconnect url
     * @param Url $url The url to output
     */
    public function outputLink(Url $url)
    {
        echo '<link rel="' . $this->rel . '" href="' . $this->app->escape->html($url->url) . '"' . $this->app->html->getAttributes($url->attributes) . ' />' . "\n";
    }
}
