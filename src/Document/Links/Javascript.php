<?php
/**
* The Javascript Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\App\LazyLoadProperty;
use Mars\Assets\Javascript as JsAsset;

/**
 * The Document's Javascript Urls Class
 * Class containing the javascript functionality used by a document
 */
class Javascript extends Urls
{
    /**
     * @see Urls::$version
     * {@inheritDoc}
     */
    public string $version {
        get {
            if (isset($this->version)) {
                return $this->version;
            }
            
            if ($this->app->config->development->enable || $this->app->config->development->assets->js) {
                $this->version = time();
            } else {
                $this->version = $this->getVersion('js-version');
            }

            return $this->version;
        }
    }

    /**
     * @see Urls::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'script';

    /**
     * @see Urls::$preload_config_key
     * {@inheritDoc}
     */
    public protected(set) string $preload_config_key = 'js';

    /**
     * @var JsAsset $asset The javascript asset object
     */
    #[LazyLoadProperty]
    public protected(set) JsAsset $asset;

    /**
     * @see Urls::$minify
     * {@inheritDoc}
     */
    protected bool $minify {
        get => $this->app->config->assets->js->minify->enable;
    }

    /**
     * @see Urls::$minify_exclude
     * {@inheritDoc}
     */
    protected array $minify_exclude {
        get => $this->app->config->assets->js->minify->exclude->urls;
    }

    /**
     * @see Urls::$combine
     * {@inheritDoc}
     */
    protected bool $combine {
        get => $this->app->config->assets->js->combine->enable;
    }

    /**
     * @see Urls::$combine_exclude
     * {@inheritDoc}
     */
    protected array $combine_exclude {
        get => $this->app->config->assets->js->combine->exclude->urls;
    }

    /**
     * @see Urls::$development
     * {@inheritDoc}
     */
    protected bool $development {
        get => $this->app->config->development->enable || $this->app->config->development->assets->js;
    }

    /**
     * Loads a javascript module url
     * @param string|array $urls The url(s) to load. Will only load it once, no matter how many times the function is called with the same url
     * @param string $type The type of the url [head|footer]
     * @param int $priority The url's output priority. The higher, the better
     * @param bool $preload If true, will output the url as a preload
     * @param array $attributes The attributes of the url, if any
     * @return static
     */
    public function loadModule(string|array $urls, string $type = 'head', int $priority = 100, bool $preload = false, array $attributes = []) : static
    {
        $attributes['type'] = 'module';

        return $this->load($urls, $type, $priority, $preload, $attributes);
    }

    /**
     * @see Urls::outputLink()
     * {@inheritDoc}
     */
    public function outputLink(string $url, array $attributes = [], bool $add_version = true)
    {
        if ($add_version) {
            $url = $this->getUrl($url);
        }

        echo '<script src="' . $this->app->escape->html($url) . '"' . $this->app->html->getAttributes($attributes) . '></script>' . "\n";
    }

    /**
     * Outputs the given javascript $code
     * @param string $code The code to output
     */
    public function outputCode(string $code)
    {
        echo '<script' . $this->getNonce() . '>' . "\n";
        echo $code . "\n";
        echo '</script>' . "\n";
    }

    /**
     * Encodes $data
     * @param mixed $data The data to encode
     * @return string The encoded data
     */
    public function encode($data) : string
    {
        return $this->app->json->encode($data, true);
    }

    /**
     * Decodes $data
     * @param string $data The data to decode
     * @return mixed The decoded string
     */
    public function decode(string $data)
    {
        return $this->app->json->decode($data, true);
    }
}
