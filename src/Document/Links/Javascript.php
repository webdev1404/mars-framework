<?php
/**
* The Javascript Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\App\LazyLoadProperty;
use Mars\Assets\Javascript as JsAsset;
use Mars\Document\Url;

/**
 * The Document's Javascript Urls Class
 * Class containing the javascript functionality used by a document
 */
class Javascript extends Links
{
    /**
     * @see Links::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'script';

    /**
     * @see Links::$version
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
     * Config options to pass as javascript options, if any
     */
    public array $config = [];

    /**
     * @var JsAsset $asset The javascript asset object
     */
    #[LazyLoadProperty]
    public protected(set) JsAsset $asset;

    /**
     * @see Links::$minify
     * {@inheritDoc}
     */
    protected bool $minify {
        get => $this->app->config->assets->js->minify->enable;
    }

    /**
     * @see Links::$minify_exclude
     * {@inheritDoc}
     */
    protected array $minify_exclude {
        get => $this->app->config->assets->js->minify->exclude->urls;
    }

    /**
     * @see Links::$development
     * {@inheritDoc}
     */
    protected bool $development {
        get => $this->app->config->development->enable || $this->app->config->development->assets->js;
    }

    /**
     * Loads a javascript module url
     * @see Links::add()
     * {@inheritDoc}
     */
    public function addModule(string|array $urls, string $location = 'head', int $priority = 100, array $attributes = [], bool $early_hints = false, bool $preload = false, bool $crossorigin = false) : static
    {
        $attributes['type'] = 'module';

        return $this->add($urls, $location, $priority, $attributes, $early_hints, $preload, $crossorigin);
    }

    /**
     * @see Links::outputLink()
     * {@inheritDoc}
     */
    public function outputLink(Url $url)
    {
        echo '<script src="' . $this->app->escape->html($url->url) . '"' . $this->app->html->getAttributes($url->attributes) . '></script>' . "\n";
    }

    /**
     * @see Links::outputCodes()
     * {@inheritDoc}
     */
    public function outputCodes(string $location)
    {
        if ($location == 'head') {
            $this->outputConfig();
        }

        parent::outputCodes($location);
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
     * Outputs the javascript config as a global variable, if any
     */
    protected function outputConfig()
    {
        if (!$this->config) {
            return;
        }

        $this->outputCode('window.MarsConfigData = ' . $this->encode($this->config) . ';');
    }

    /**
     * Adds a config option to be passed as a javascript option
     * @param string|array $key The config key to add. If an array is passed, the $value parameter is ignored and the array is merged with the existing config
     * @param mixed $value The config value to add
     * @return static
     */
    public function addConfig(string|array $key, mixed $value = '') : static
    {
        if (is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $value;
        }

        return $this;
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
