<?php
/**
* The List Group Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The List Group Trait
 * Encapsulates a list where elements are grouped by type.
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait ListGroupTrait
{
    /**
     * Check if a specific element exists in the list.
     * @param string $type The type of the element to check.
     * @param mixed $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function has(string $type, mixed $value) : bool
    {
        if (!isset($this->{static::$property}[$type])) {
            return false;
        }

        return in_array($value, $this->{static::$property}[$type]);
    }

    /**
     * Returns elements
     * @param string $type The type of the elements to return. If empty, all elements are returned.
     * @return array The elements
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
     * @param string|array $type The type of the elements. If an array is provided, the elements will be added to the corresponding types. If a string is provided, the elements will be added to that type.
     * @param mixed $value The value
     * @return static
     */
    public function add(string|array $type, mixed $value = '') : static
    {
        if (is_array($type)) {
            $this->addMany($type);
        } else {
            $this->{static::$property}[$type][] = $value;
        }

        return $this;
    }

    /**
     * Adds a list of elements
     * @param string $type The type of the elements
     * @param array $values The values to add
     * @return static
     */
    public function addMany(string|array $type, array $values = []) : static
    {
        if (is_array($type)) {
            foreach ($type as $_type => $values) {
                $this->{static::$property}[$_type] = array_merge($this->{static::$property}[$_type] ?? [], (array)$values);
            }
        } else {
            foreach ($values as $value) {
                $this->{static::$property}[$type][] = $value;
            }
        }

        return $this;
    }

    /**
     * Sets the elements for a specific type
     * @param string $type The type of the elements
     * @param array $values The values to set
     * @return static
     */
    public function set(string $type, array $values) : static
    {        
        $this->{static::$property}[$type] = $values;

        return $this;
    }

    /**
     * Removes an element
     * @param string $type The type of the elements
     * @param string|array $values The value(s) to remove
     * @return static
     */
    public function remove(string $type, string|array $values) : static
    {
        $values = (array)$values;

        foreach ($values as $value) {
            $key = array_search($value, $this->{static::$property}[$type] ?? []);
            if ($key !== false) {
                unset($this->{static::$property}[$type][$key]);
            }
        }

        return $this;
    }

    /**
     * Returns the number of elements in the list for a specific type
     * @param string $type The type of the elements to count
     * @return int
     */
    public function count(string $type) : int
    {
        return count($this->{static::$property}[$type] ?? []);
    }

    /**
     * Returns the list as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \RecursiveArrayIterator($this->{static::$property});
    }
}
