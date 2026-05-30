<?php
/**
* The URL Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
 * The Document URL Class
 * Class containing a document URL
 */
class Url extends \Mars\Url
{
    /**
     * @var string $type The URL's type
     */
    public protected(set) string $type = '';

    /**
     * @var array $attributes The URL's attributes, if any
     */
    public protected(set) array $attributes = [];

    /**
     * @var int $priority The URL's priority (higher number means higher priority)
     */
    public protected(set) int $priority = 100;

    /**
     * Builds the URL object
     * @param string|array $url The URL or an array containing the URL and its attributes
     * @param string $type The URL's type
     * @param array $attributes The URL's attributes, if any
     * @param int $priority The URL's priority (higher number means higher priority)
     * @param App|null $app The app instance
     */
    public function __construct(string|array $url, string $type, array $attributes = [], int $priority = 100, ?App $app = null)
    {
        $this->app = $app;
        $this->type = $type;

        if (is_array($url)) {
            $attributes = $url['attributes'] ?? [];
            $priority = $url['priority'] ?? $priority;
            $url = $url['url'];
        }

        $this->url = $this->getUrl($url);
        $this->attributes = $attributes;
        $this->priority = $priority;
    }

    /**
     * Processes the URL, applying the version if it's local
     * @param string $url The URL to process
     * @return string The processed URL
     */
    protected function getUrl(string $url) : string
    {
        if (!$this->type) {
            return $url;
        }

        static $handlers = [
            'script' => $this->app->document->js,
            'style' => $this->app->document->css,
        ];

        if (!isset($handlers[$this->type])) {
            return $url;
        }

        //we need the $this->url property set in order to check if the url is local or not, so we set it here temporarily, it will be overridden later with the final url
        $this->url = $url;

        if (!$this->is_local) {
            return $url;
        }

        $handler = $handlers[$this->type];

        return $handler->getUrl($url, true);
    }
}
