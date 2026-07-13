<?php
/**
* The Html Response Body Data Class
* @package Mars
*/

namespace Mars\Http\Response\Body\Data;

/**
 * The Html Response Body Data Class
 */
class Html extends Data
{
    /**
     * @internal
     */
    public protected(set) string $type = 'html';

    /**
     * @internal
     */
    public function __toString() : string
    {
        return (string)$this->content;
    }
}
