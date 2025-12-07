<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

/**
 * The Document's Css Urls Class
 * Class containing the css functionality used by a document
 */
class Css extends Urls
{
    /**
     * @see Urls::$version
     * {@inheritdoc}
     */
    public string $version {
        get => $this->app->config->document->css->version;
    }

    /**
     * @see Urls::$type
     * {@inheritdoc}
     */
    public protected(set) string $type = 'style';

    /**
     * @see Urls::$preload_config_key
     * {@inheritdoc}
     */
    public protected(set) string $preload_config_key = 'css';

    /**
     * @see Urls::outputLink()
     * {@inheritdoc}
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
