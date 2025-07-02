<?php
/**
* The List Trait
* @package Mars
*/

namespace Mars\Data;

use Mars\App;

/**
 * The List Trait
 * Encapsulates a simple list
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait ListTrait
{
    /**
     * The name of the property which holds the list
     */
    //protected static string $property = 'list';

    /**
     * Check if a specific element exists in the list.
     * @param string $value The value of the element to check.
     * @return bool Returns true if the element exists, false otherwise.
     */
    public function exists(string $value) : bool
    {
         if (array_search($value, $this->{static::$property}) === false) {
             return false;
         }

        return true;
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
     * @return string The alert
     */
    public function getFirst()
    {
        if (!$this->{static::$property}) {
            return '';
        }

        return reset($this->{static::$property});
    }

     /**
     * Returns the last element
     * @return string The alert
     */
    public function getLast()
    {
        if (!$this->{static::$property}) {
            return '';
        }

        return end($this->{static::$property});
    }

    /**
     * Adds an element
     * @param string|array $values The value(s)
     * @return static
     */
    public function add(string|array $values) : static
    {
        $values = (array)$values;

        $this->{static::$property} = array_merge($this->{static::$property}, $values);

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

        $this->{static::$property} = $values;

        return $this;
    }

    /**
     * Resets the list
     * @return static
     */
    public function reset() : static
    {
        $this->{static::$property} = [];

        return $this;
    }

    /**
     * Removes an element
     * @param string|array $values The value(s) to remove
     * @return static
     */
    public function remove(string|array $values) : static
    {
        $this->{static::$property} = App::remove($this->{static::$property}, $values);

        return $this;
    }

    /**
     * Returns the number of elements in the list
     * @return int
     */
    public function count() : int
    {
        return count($this->{static::$property});
    }

    /**
     * Returns the list as an iterator
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->{static::$property});
    }
}
