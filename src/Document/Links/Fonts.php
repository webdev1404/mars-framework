<?php
/**
* The Fonts Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

/**
 * The Fonts Urls Class
 * Class containing the fonts functionality used by a document
 */
class Fonts extends Urls
{
    /**
     * @see Urls::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'font';

    /**
     * @see Urls::$preload_config_key
     * {@inheritDoc}
     */
    public protected(set) string $preload_config_key = 'fonts';

    /**
     * @see Urls::$crossorigin
     * {@inheritDoc}
     */
    public protected(set) string $crossorigin = 'anonymous';

    /**
     * Does nothing
     */
    public function outputLink(string $url, array $attributes = [], bool $add_version = true)
    {
    }

    /**
     * Does nothing
     */
    public function outputCode(string $code)
    {
    }
}
