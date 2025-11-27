<?php
/**
* The String Class
* @package Mars
*/

namespace Mars\Data\Types;

/**
 * The String Class
 * Handles string operations
 */
class StringType
{
    /**
     * Returns a string from a value
     * @param mixed $value The value
     * @return string The string
     */
    public function get(mixed $value) : string
    {
        if (is_array($value)) {
            return (string)array_first($value);
        }

        return (string)$value;
    }
}
