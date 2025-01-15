<?php
/**
* The Form Input Interface
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Form Input Interface
 */
interface FormInputInterface
{    
    /**
     * Returns the name attribute
     * @return string
     */
    public function getNameAttribute() : string;

    /**
     * Returns the value attribute
     * @return string
     */
    public function getValueAttribute() : string;

    /**
     * Checks if the value is allowed
     * @param string|array $value The value to check
     * @param array $attributes The attributes of the input
     * @return bool
     */
    public function isAllowedValues(string|array $value, array $attributes) : bool;
}
