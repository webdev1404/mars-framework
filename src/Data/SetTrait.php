<?php
/**
* The Set Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Set Trait
 * Encapsulates a list where elements are grouped by type.
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait SetTrait
{
    /**
     * Check if a specific element exists in the list.
     * @param string $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $value) : bool
    {
        foreach ($this->{static::$property} as $type => $values) {
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
            return $this->{static::$property}[$type] ?? [];
        }

        return $this->{static::$property};
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

        $this->{static::$property}[$type] = array_merge($this->{static::$property}[$type] ?? [], $values);

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
            $this->{static::$property}[$type] = $this->app->array->remove($this->{static::$property}[$type] ?? [], $values);

            return $this;
        }


        foreach ($this->{static::$property} as $key => $list_values) {
            $this->{static::$property}[$key] = $this->app->array->remove($list_values, $values);
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
        foreach ($this->{static::$property} as $values) {
            $count += count($values);
        }

        return $count;
    }

    /**
     * Returns the list as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \RecursiveArrayIterator($this->{static::$property});
    }
}
