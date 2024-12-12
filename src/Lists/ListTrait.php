<?php
/**
* The List Trait
* @package Mars
*/

namespace Mars\Lists;

/**
 * The List Trait
 * Encapsulates a list
 */
trait ListTrait
{
    use ListSimpleTrait;

    /**
     * Check if a specific element exists in the list.
     * @param string $name The name of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $name) : bool
    {
        return isset($this->list[$name]);
    }

    /**
     * Returns an element, or all elements
     * @param string $name If specified, will return only this element
     */
    public function get(string $name = '')
    {
        if (!$name) {
            return $this->list;
        }

        return $this->list[$name] ?? null;
    }

    /**
     * Adds an element
     * @param string $name The name of the element
     * @param string $value The value
     * @return static
     */
    public function add(string $name, string $value) : static
    {
        $this->list[$name] = $value;

        return $this;
    }

    /**
     * Sets the list
     * @param array $list The list of elements in the name => value format
     * @return static
     */
    public function set(array $list) : static
    {
        $this->list = $list;
        
        return $this;
    }

    /**
     * Removes an element
     * @param string $name The name of the element
     * @return static
     */
    public function remove(string $name) : static
    {
        if (isset($this->list[$name])) {
            unset($this->list[$name]);
        }

        return $this;
    }
}
