<?php
/**
* The Iterator Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Iterator Trait
 * Encapsulates a simple iterator
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the iterator.
 */
trait IteratorTrait
{
    /**
     * Check if a specific element exists in the iterator.
     * @param mixed $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function has(mixed $value) : bool
    {
        return in_array($value, $this->{static::$property});
    }

    /**
     * Returns all elements
     */
    public function get() : array
    {
        return $this->{static::$property};
    }

    /**
     * Returns the first element
     * @return mixed The first element or null if the iterator is empty
     */
    public function getFirst() : mixed
    {
        return array_first($this->{static::$property});
    }

    /**
     * Returns the last element
     * @return mixed The last element or null if the iterator is empty
     */
    public function getLast() : mixed
    {
        return array_last($this->{static::$property});
    }

    /**
     * Sets the iterator
     * @param array $values The values
     * @return static
     */
    public function set(array $values) : static
    {
        $this->{static::$property} = $values;

        return $this;
    }

    /**
     * Resets the iterator
     * @return static
     */
    public function reset() : static
    {
        $this->{static::$property} = [];

        return $this;
    }

    /**
     * Returns the number of elements in the iterator
     * @return int
     */
    public function count() : int
    {
        return count($this->{static::$property});
    }

    /**
     * Returns the iterator as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->{static::$property});
    }
}