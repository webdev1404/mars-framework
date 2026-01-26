<?php
/**
* The Images Urls Class
* @package Mars
*/

namespace Mars\Document\Links;

/**
 * The Images Urls Class
 * Class containing the images functionality used by a document
 */
class Images extends Urls
{
    /**
     * @see Urls::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'image';

    /**
     * @see Urls::$preload_config_key
     * {@inheritDoc}
     */
    public protected(set) string $preload_config_key = 'images';

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
