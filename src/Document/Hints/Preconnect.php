<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document\Hints;

use Mars\App;
use Mars\Document\Links\Hints\Preload;

/**
 * The Preconnect Urls Class
 * Class containing the preconnect functionality used by a document
 */
class Preconnect extends Preload
{
    /**
     * Builds the preconnect object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $urls = $this->app->config->hints->preconnect ?? [];
        if ($urls) {
            $this->load($urls);
        }
    }

    /**
     * Outputs the preconnect urls
     */
    public function output()
    {
        foreach ($this->urls as $url) {
            $this->outputLink($url);
        }
    }

    /**
     * Outputs a preconnect url
     * @param string $url The url to output
     */
    public function outputLink(string $url)
    {
        echo '<link rel="preconnect" href="' . $this->app->escape->html($url) . '" />' . "\n";
    }
}
