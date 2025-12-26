<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document\Hints;

use Mars\App;
use Mars\App\Kernel;
use Mars\Data\SetTrait;

/**
 * The Preconnect Urls Class
 * Class containing the preconnect functionality used by a document
 */
class Preconnect
{
    use Kernel;
    use SetTrait;

    /**
     * @var array $urls Array with all the urls to preconnect to
     */
    protected array $urls = ['cors' => [], 'non_cors' => []];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * Builds the preconnect object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $cors_urls = $this->app->config->hints->preconnect->cors ?? [];
        if ($cors_urls) {
            $this->load($cors_urls, true);
        }

        $non_cors_urls = $this->app->config->hints->preconnect->non_cors ?? [];
        if ($non_cors_urls) {
            $this->load($non_cors_urls);
        }
    }

    /**
     * Loads the urls to preconnect to
     * @param string|array $urls The urls to preconnect to
     * @param bool $crossorigin If true, the crossorigin attribute will be added to the link
     * @return static
     */
    public function load(string|array $urls, bool $crossorigin = false) : static
    {
        $type = $crossorigin ? 'cors' : 'non_cors';

        return $this->add($type, $urls);
    }

    /**
     * Unloads the preconnected urls
     * @param string|array $urls The urls to unload
     * @return static
     */
    public function unload(string|array $urls) : static
    {
        return $this->remove($urls);
    }

    /**
     * Outputs the preconnect urls
     */
    public function output()
    {
        foreach ($this->urls['cors'] as $url) {
            $this->outputLink($url, true);
        }

        foreach ($this->urls['non_cors'] as $url) {
            $this->outputLink($url);
        }
    }

    /**
     * Outputs a preconnect url
     * @param string $url The url to output
     * @param bool $crossorigin If true, the crossorigin attribute will be added to the link
     */
    public function outputLink(string $url, bool $crossorigin = false)
    {
        $crossorigin = $crossorigin ? ' crossorigin="anonymous"' : '';

        echo '<link rel="preconnect" href="' . $this->app->escape->html($url) . '"' . $crossorigin . ' />' . "\n";
    }
}
