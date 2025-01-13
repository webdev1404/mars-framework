<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;
use Mars\Document\Urls\Preload;

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

        $urls = $this->app->config->preconnect ?? [];
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
            $this->outputUrl($url);
        }
    }

    /**
     * Outputs a preconnect url
     * @param string $url The url to output
     */
    public function outputUrl(string $url)
    {
        echo '<link rel="preconnect" href="'. $this->app->escape->html($url) .'" />' . "\n";
    }
}
