<?php
/**
* The Fonts Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Fonts Urls Class
 * Class containing the fonts functionality used by a document
 */
class Fonts extends Urls
{
    /**
     * @see \Mars\Document\Urls::$type
     * {@inheritdoc}
     */
    public protected(set) string $type = 'font';

    /**
     * @see \Mars\Document\Urls::$preload_config_key
     * {@inheritdoc}
     */
    public protected(set) string $preload_config_key = 'fonts';

    /**
     * @see \Mars\Document\Urls::$crossorigin
     * {@inheritdoc}
     */
    public protected(set) string $crossorigin = 'anonymous';

    /**
     * Does nothing
     */
    public function outputUrl(string $url, array $attributes = [])
    {
    }
}
