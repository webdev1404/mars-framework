<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document\Hint;

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
     * Renders the preconnect urls
     */
    public function render()
    {
        $this->load();

        foreach ($this->urls as $url) {
            $this->renderLink($url);
        }
    }

    /**
     * Loads the preconnect urls from the config
     */
    protected function load()
    {
        $this->addMany($this->app->config->document->hints->preconnect);
    }

    /**
     * Renders a preconnect url
     * @param Url $url The url to render
     */
    public function renderLink(Url $url)
    {
        echo '<link rel="' . $this->rel . '" href="' . $this->app->escape->html($url->url) . '"' . $this->app->html->getAttributes($url->attributes) . ' />' . "\n";
    }
}
