<?php
/**
* The Preload Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App\InstanceTrait;

/**
 * The Preload Urls Class
 * Class containing the preload functionality used by a document
 */
class Preload 
{
    use InstanceTrait;

    /**
     * @var string $rel The rel attribute of the preload
     */
    protected string $rel = 'preload';

    /**
     * @var array $preload_list The list with the items which can be preloaded
     */
    protected array $list = ['css', 'javascript', 'fonts', 'images'];

    /**
     * @var array $types The types of the urls which can be preloaded and the object which will handle it
     */
    protected array $types = [
        'css' => 'css',
        'style' => 'css',
        'javascript' => 'javascript',
        'script' => 'javascript',
        'images' => 'images',
        'image' => 'images',
        'font' => 'fonts',
        'fonts' => 'fonts',
    ];

    /**
     * Adds an url to the preload list
     * @param string $type The type of the url [css|javascript|fonts|images]
     * @param string|array $url The url(s) to load
     * @param int $priority The url's output priority. The higher, the better
     * @param bool $early_hints If true, will output the url as an early hint
     * @param array $attributes The attributes of the url, if any
     * @return static
     * @throws \Exception
     */
    public function load(string $type, string|array $url, int $priority = 100, bool $early_hints = false, array $attributes = []) : static
    {
        if (!isset($this->types[$type])) {
            throw new \Exception("Invalid preload type: {$type} for url {$url}");
        }

        $item = $this->types[$type];

        $this->app->document->$item->load($url, $this->rel, $priority, $early_hints, $attributes);

        return $this;
    }

    /**
     * Unloads an url from the preload list
     * @param string $type The type of the url [css|javascript|fonts|images]
     * @param string|array $url The url(s) to unload
     * @return static
     * @throws \Exception
     */
    public function unload(string $type, string|array $url) : static
    {
        if (!isset($this->types[$type])) {
            throw new \Exception("Invalid preload type: {$type} for url {$url}");
        }

        $item = $this->types[$type];

        $this->app->document->$item->unload($url);

        return $this;
    }

    /**
     * Outputs the preload urls
     */
    public function output()
    {
        foreach ($this->list as $item) {
            $obj = $this->app->document->$item;

            $urls = $obj->get($this->rel);
            
            foreach ($urls as $url) {
                $this->outputUrl($obj, $url['url']);
            }
        }
    }

    /**
     * Outputs the given url
     * @param Urls $item The item the url belongs to
     * @param string $url The url to output
     */
    public function outputUrl(Urls $item, string $url)
    {
        $crossorigin = $item->crossorigin ? ' crossorigin="' . $item->crossorigin . '"' : '';

        echo '<link rel="'. $this->rel .'" href="' . $this->app->escape->html($url) . '" as="'. $item->type .'"'. $crossorigin .' />' . "\n";
    }
}