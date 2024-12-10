<?php
/**
* The Images Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Images Urls Class
 * Class containing the images functionality used by a document
 */
class Images extends Urls
{
    /**
     * @see \Mars\Document\Urls::$type
     * {@inheritdoc}
     */
    public protected(set) string $type = 'image';

    /**
     * Does nothing
     */
    public function outputUrl(string $url, array $attributes = [])
    {
    }
}
