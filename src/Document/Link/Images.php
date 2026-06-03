<?php
/**
* The Images Links Class
* @package Mars
*/

namespace Mars\Document\Link;

use Mars\Document\Url;

/**
 * The Images Links Class
 * Class containing the images functionality used by a document
 */
class Images extends Links
{
    /**
     * @see Links::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'image';

    /**
     * Does nothing
     */
    public function renderLink(Url $url)
    {
    }

    /**
     * Does nothing
     */
    public function renderCode(string $code)
    {
    }
}
