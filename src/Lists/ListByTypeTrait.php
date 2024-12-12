<?php
/**
* The ListByType Trait
* @package Mars
*/

namespace Mars\Lists;

use Mars\App;

/**
 * The ListByType Trait
 * Encapsulates a list by type
 */
trait ListByTypeTrait
{
    use ListSimpleTrait;

    /**
     * Check if a specific element exists in the list.
     * @param string $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $value) : bool
    {
        foreach ($this->list as $type => $values) {
            if (array_search($value, $values) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all elements
     * @param string $type The type of the elements to return. If empty, all elements are returned.
     */
    public function get(string $type = '') : array
    {
        if ($type) {
            return $this->list[$type] ?? [];
        }

        return $this->list;
    }

    /**
     * Adds an element
     * @param string $type The type of the elements
     * @param string|array $values The value(s)
     * @return static
     */
    public function add(string $type, string|array $values) : static
    {
        $values = (array)$values;

        $this->list[$type] = array_merge($this->list[$type] ?? [], $values);

        return $this;
    }

    /**
     * Removes an element
     * @param string|array $values The value(s) to remove
     * @param string $type The type of the elements
     * @return static
     */
    public function remove(string|array $values, string $type = '') : static
    {
        if ($type) {
            $this->list[$type] = App::remove($this->list[$type] ?? [], $values);

            return $this;
        }


        foreach ($this->list as $key => $list_values) {
            $this->list[$key] = App::remove($list_values, $values);
        }

        return $this;
    }

    /**
     * Returns the number of elements in the list
     * @return int
     */
    public function count() : int
    {
        $count = 0;
        foreach ($this->list as $values) {
            $count += count($values);
        }

        return $count;
    }

    /**
     * Returns the list as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \RecursiveArrayIterator($this->list);
    }
}
