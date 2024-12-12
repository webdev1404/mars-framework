<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Document's Css Urls Class
 * Class containing the css functionality used by a document
 */
class Css extends Urls
{
    /**
     * @see \Mars\Document\Urls::$version
     * {@inheritdoc}
     */
    public protected(set) string $version {
        get => $this->app->config->css_version;
    }

    /**
     * @see \Mars\Document\Urls::$type
     * {@inheritdoc}
     */
    public protected(set) string $type = 'style';

    /**
     * @see \Mars\Document\Urls::$preload_config_key
     * {@inheritdoc}
     */
    public protected(set) string $preload_config_key = 'css';

    /**
     * @see \Mars\Document\Urls::outputUrl()
     * {@inheritdoc}
     */
    public function outputUrl(string $url, array $attributes = [])
    {
        echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url) . '" />' . "\n";
    }

    /**
     * Outputs the given css $code
     * @param string $code The code to output
     */
    public function outputCode(string $code)
    {
        echo '<style type="text/css">' . "\n";
        echo $code . "\n";
        echo '</style>' . "\n";
    }
}
