<?php
/**
* The Javascript Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Document's Javascript Urls Class
 * Class containing the javascript functionality used by a document
 */
class Javascript extends Urls
{
    /**
     * @see \Mars\Document\Urls::$version
     * {@inheritdoc}
     */
    protected string $version {
        get => $this->app->config->javascript_version;
    }

    /**
     * @see \Mars\Document\Urls::outputPreloadUrl()
     * {@inheritdoc}
     */
    public function outputPreloadUrl(string $url)
    {
        echo '<link rel="preload" href="' . $this->app->escape->html($url) . '" as="script" />' . "\n";
    }

    /**
     * @see \Mars\Document\Urls::outputUrl()
     * {@inheritdoc}
     */
    public function outputUrl(string $url, array $attributes = [])
    {
        echo '<script type="text/javascript" src="' . $this->app->escape->html($url) . '"' . $this->getAttributes($attributes) . '></script>' . "\n";
    }

    /**
     * Outputs the given javascript $code
     * @param string $code The code to output
     */
    public function outputCode(string $code)
    {
        echo '<script type="text/javascript">' . "\n";
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
        return json_encode($data);
    }

    /**
     * Decodes $data
     * @param string $data The data to decode
     * @return mixed The decoded string
     */
    public function decode(string $data)
    {
        return json_decode($data, true);
    }
}
