<?php
/**
* The Fonts Links Class
* @package Mars
*/

namespace Mars\Document\Links;

use Mars\Document\Url;

/**
 * The Fonts Links Class
 * Class containing the fonts functionality used by a document
 */
class Fonts extends Links
{
    /**
     * @see Links::$type
     * {@inheritDoc}
     */
    public protected(set) string $type = 'font';

    /**
     * @see Links::$crossorigin
     * {@inheritDoc}
     */
    public protected(set) string $crossorigin = 'anonymous';

    /**
     * Does nothing
     */
    public function outputLink(Url $url)
    {
    }

    /**
     * Does nothing
     */
    public function outputCode(string $code)
    {
    }
}
