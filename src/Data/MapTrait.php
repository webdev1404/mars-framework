<?php
/**
* The Map Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Map Trait
 * Encapsulates a map where elements are stored in a name => value format.
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait MapTrait
{
    use ListTrait;

    /**
     * Check if a specific element exists in the list.
     * @param string $name The name of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $name) : bool
    {
        return isset($this->{static::$property}[$name]);
    }

    /**
     * Returns an element, or all elements
     * @param string $name If specified, will return only this element
     */
    public function get(string $name = '')
    {
        if (!$name) {
            return $this->{static::$property};
        }

        return $this->{static::$property}[$name] ?? null;
    }

    /**
     * Alias for set()
     */
    public function add(string $name, mixed $value) : static
    {
        return $this->set($name, $value);
    }

    /**
     * Sets an element
     * @param string $name The name of the element
     * @param mixed $value The value
     * @return static
     */
    public function set(string $name, mixed $value) : static
    {
        $this->{static::$property}[$name] = $value;

        return $this;
    }

    /**
     * Assign the list
     * @param array $list The list of elements in the name => value format
     * @return static
     */
    public function assign(array $list) : static
    {
        $this->{static::$property} = $list;
        
        return $this;
    }

    /**
     * Removes an element
     * @param string $name The name of the element
     * @return static
     */
    public function remove(string $name) : static
    {
        if (isset($this->{static::$property}[$name])) {
            unset($this->{static::$property}[$name]);
        }

        return $this;
    }
}
