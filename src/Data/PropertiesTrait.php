<?php
/**
* The Data Properties Trait
* @package Mars
*/

namespace Mars\Data;

/**
* The Data Properties Trait
 * Represent data stored in the format name => value
 */
trait PropertiesTrait
{
    /**
     * Determines if a property with the given name exists and is not empty
     * @param string $name The name of the property
     * @return bool
     */
    public function exists(string $name) : bool
    {
        return !empty($this->$name);
    }

    /**
     * Assigns the data to the object
     * @param array $data Array in the name=>value format
     * @return static
     */
    public function assign(array $data) : static
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }
}
