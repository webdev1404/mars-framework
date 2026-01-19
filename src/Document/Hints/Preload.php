<?php
/**
* The Preload Urls Class
* @package Mars
*/

namespace Mars\Document\Hints;

use Mars\App\Kernel;
use Mars\Document\Links\Urls;

/**
 * The Preload Urls Class
 * Class containing the preload functionality used by a document
 */
class Preload
{
    use Kernel;

    /**
     * @var string $rel The rel attribute of the preload
     */
    protected string $rel = 'preload';

    /**
     * @var array $preload_list The list with the items which can be preloaded
     */
    protected array $list = ['css', 'js', 'fonts', 'images'];

    /**
     * @var array $types The types of the urls which can be preloaded and the object which will handle it
     */
    protected array $types = [
        'css' => 'css',
        'style' => 'css',
        'js' => 'js',
        'javascript' => 'js',
        'script' => 'js',
        'images' => 'images',
        'image' => 'images',
        'font' => 'fonts',
        'fonts' => 'fonts',
    ];

    /**
     * Adds an url to the preload list
     * @param string|array $url The url(s) to load
     * @param string $type The type of the url [css|js|fonts|images]
     * @param int $priority The url's output priority. The higher, the better
     * @param array $attributes The attributes of the url, if any
     * @return static
     * @throws \Exception
     */
    public function load(string|array $url, string $type) : static
    {
        if (!isset($this->types[$type])) {
            throw new \Exception("Invalid preload type: {$type}");
        }

        $item = $this->types[$type];

        $this->app->document->$item->preload($url);

        return $this;
    }

    /**
     * Unloads an url from the preload list
     * @param string|array $url The url(s) to unload
     * @param string $type The type of the url [css|js|fonts|images]
     * @return static
     * @throws \Exception
     */
    public function unload(string|array $url, string $type) : static
    {
        if (!isset($this->types[$type])) {
            throw new \Exception("Invalid preload type: {$type}");
        }

        $item = $this->types[$type];

        $this->app->document->$item->unloadPreload($url);

        return $this;
    }

    /**
     * Outputs the preload urls
     */
    public function output()
    {
        $rel = $this->rel;
        foreach ($this->list as $item) {
            $obj = $this->app->document->$item;

            foreach ($obj->$rel->get() as $url) {
                $this->outputLink($obj, $url);
            }
        }
    }

    /**
     * Outputs the given link
     * @param Urls $item The item the url belongs to
     * @param string $url The url to output
     */
    public function outputLink(Urls $item, string $url)
    {
        $crossorigin = $item->crossorigin ? ' crossorigin="' . $item->crossorigin . '"' : '';

        echo '<link rel="' . $this->rel . '" href="' . $this->app->escape->html($url) . '" as="' . $item->type . '"' . $crossorigin . ' />' . "\n";
    }
}
