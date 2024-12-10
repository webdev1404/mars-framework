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
    public protected(set) string $version {
        get => $this->app->config->javascript_version;
    }

    /**
     * @see \Mars\Document\Urls::$type
     * {@inheritdoc}
     */
    public protected(set) string $type = 'script';

    /**
     * @see \Mars\Document\Urls::outputUrl()
     * {@inheritdoc}
     */
    public function outputUrl(string $url, array $attributes = [])
    {
        echo '<script type="text/javascript" src="' . $this->app->escape->html($url) . '"' . $this->app->html->getAttributes($attributes) . '></script>' . "\n";
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
        return $this->app->json->encode($data);
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
