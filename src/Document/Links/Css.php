<?php
/**
* The Css Links Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\App\LazyLoadProperty;
use Mars\Assets\Css as CssAsset;
use Mars\Document\Url;

/**
 * The Document's Css Links Class
 * Class containing the css functionality used by a document
 */
class Css extends Links
{
    /**
     * @see Links::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'style';

    /**
     * @see Links::$version
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
     * @var CssAsset $asset The css asset object
     */
    #[LazyLoadProperty]
    public protected(set) CssAsset $asset;

    /**
     * @see Links::$minify
     * {@inheritDoc}
     */
    protected bool $minify {
        get => $this->app->config->assets->css->minify->enable;
    }

    /**
     * @see Links::$minify_exclude
     * {@inheritDoc}
     */
    protected array $minify_exclude {
        get => $this->app->config->assets->css->minify->exclude->urls;
    }

    /**
     * @see Links::$development
     * {@inheritDoc}
     */
    protected bool $development {
        get => $this->app->config->development->enable || $this->app->config->development->assets->css;
    }

    /**
     * @see Links::outputLink()
     * {@inheritDoc}
     */
    public function outputLink(Url $url)
    {
        echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url->url) . '" />' . "\n";
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
