<?php
/**
* The Json Response Body Data Class
* @package Mars
*/

namespace Mars\Http\Response\Body\Data;

/**
 * The Json Response Body Data Class
 */
class Json extends Data
{
    /**
     * @internal
     */
    public protected(set) string $type = 'json';
}
