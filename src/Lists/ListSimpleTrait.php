<?php
/**
* The Simple List Trait
* @package Mars
*/

namespace Mars\Lists;

use Mars\App;

/**
 * The List Trait
 * Encapsulates a simple list
 */
trait ListSimpleTrait
{
    /**
     * @var array $list The list of elements
     */
    public protected(set) array $list = [];

    /**
     * Check if a specific element exists in the list.
     * @param string $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $value) : bool
    {
        return array_search($value, $this->list);
    }

    /**
     * Returns all elements
     */
    public function get() : array
    {
        return $this->list;
    }

    /**
     * Adds an element
     * @param string|array $values The value(s)
     * @return static
     */
    public function add(string|array $values) : static
    {
        $values = (array)$values;

        $this->list = array_merge($this->list, $values);

        return $this;
    }

    /**
     * Sets the list
     * @param string|array $values The value(s)
     * @return static
     */
    public function set(string|array $values) : static
    {
        $values = (array)$values;

        $this->list = $values;

        return $this;
    }

    /**
     * Removes an element
     * @param string|array $values The value(s) to remove
     * @return static
     */
    public function remove(string|array $values) : static
    {
        $this->list = App::remove($this->list, $values);

        return $this;
    }

    /**
     * Returns the number of elements in the list
     * @return int
     */
    public function count() : int
    {
        return count($this->list);
    }

    /**
     * Returns the list as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->list);
    }
}
