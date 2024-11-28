<?php
/**
* The Property Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App\InstanceTrait;

/**
 * The Document Property Class
 * Stores the value of a document's property. Eg: title
 */
abstract class Property
{
    use InstanceTrait;

    /**
     * @var string $value The property's value
     */
    public string $value = '';

    /**
     * Returns the value of the property
     * @return string
     */
    public function get() : string
    {
        return $this->value;
    }

    /**
     * Sets the value of the property
     * @param string $value The new value
     * @return static
     */
    public function set(string $value) : static
    {
        $this->value = $value;

        return $this;
    }
}
