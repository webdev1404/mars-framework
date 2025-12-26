<?php
/**
* The ArrayResult Config Class
* @package Mars
*/

namespace Mars\Config;

/**
 * The ArrayResult Container Class
 * Holds an array result
 */
class ArrayResult
{
    /**
     * @var array $values The values
     */
    public array $values = [];

    /**
     * Builds the ArrayResult object
     * @param array $values The values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
}
