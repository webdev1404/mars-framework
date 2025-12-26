<?php
/**
* The Data Accessor Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Data Accessor Trait
 * Represent data stored in the format name => value
 */
trait AccessorTrait
{
    /**
     * Determines if a property with the given name exists
     * @param string $name The name of the property
     * @return bool
     */
    public function exists(string $name) : bool
    {
        return isset($this->$name);
    }

    /**
     * Returns the value for the specified property name
     * @param string $name The name of the property
     * @param mixed $default The default value to return if the property is not set
     * @return mixed The property value
     */
    public function get(string $name, mixed $default = null) : mixed
    {
        return $this->$name ?? $default;
    }

    /**
     * Sets the config value for the specified $name
     * @param string $name The name of the config option
     * @param mixed $value The value to set
     * @return static
     */
    public function set(string $name, mixed $value) : static
    {
        $this->$name = $value;

        return $this;
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
