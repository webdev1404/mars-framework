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
}
