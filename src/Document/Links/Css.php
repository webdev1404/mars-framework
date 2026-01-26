<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\LazyLoadProperty;
use Mars\Assets\Css as CssAsset;

/**
 * The Document's Css Urls Class
 * Class containing the css functionality used by a document
 */
class Css extends Urls
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
            
            if ($this->app->config->development->enable || $this->app->config->development->assets->css) {
                $this->version = time();
            } else {
                $this->version = $this->getVersion('css-version');
            }

            return $this->version;
        }
    }

    /**
     * @see Urls::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'style';

    /**
     * @see Urls::$preload_config_key
     * {@inheritDoc}
     */
    public protected(set) string $preload_config_key = 'css';

    /**
     * @var CssAsset $asset The css asset object
     */
    #[LazyLoadProperty]
    public protected(set) CssAsset $asset;

    /**
     * @see Urls::$minify
     * {@inheritDoc}
     */
    protected bool $minify {
        get => $this->app->config->assets->css->minify->enable;
    }

    /**
     * @see Urls::$minify_exclude
     * {@inheritDoc}
     */
    protected array $minify_exclude {
        get => $this->app->config->assets->css->minify->exclude->urls;
    }

    /**
     * @see Urls::$combine
     * {@inheritDoc}
     */
    protected bool $combine {
        get => $this->app->config->assets->css->combine->enable;
    }

    /**
     * @see Urls::$combine_exclude
     * {@inheritDoc}
     */
    protected array $combine_exclude {
        get => $this->app->config->assets->css->combine->exclude->urls;
    }

    /**
     * @see Urls::$development
     * {@inheritDoc}
     */
    protected bool $development {
        get => $this->app->config->development->enable || $this->app->config->development->assets->css;
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
        
        echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url) . '" />' . "\n";
    }

    /**
     * Outputs the given css code
     * @param string $code The code to output
     */
    public function outputCode(string $code)
    {
        echo '<style type="text/css"' . $this->getNonce() . '>' . "\n";
        echo $code . "\n";
        echo '</style>' . "\n";
    }
}
