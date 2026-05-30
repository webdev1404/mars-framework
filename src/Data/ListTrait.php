<?php
/**
* The List Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The List Trait
 * Encapsulates a simple list
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait ListTrait
{
    use IteratorTrait;
    
    /**
     * The name of the property which holds the list
     */
    //protected static string $property = 'list';

    /**
     * Adds an element
     * @param mixed $value The value to add
     * @return static
     */
    public function add(mixed $value) : static
    {
        $this->{static::$property}[] = $value;

        return $this;
    }

    /**
     * Adds multiple elements
     * @param array $values The values to add
     * @return static
     */
    public function addMany(array $values) : static
    {
        $this->{static::$property} = array_merge($this->{static::$property}, $values);

        return $this;
    }

    /**
     * Removes an element
     * @param string|array $values The value(s) to remove
     * @return static
     */
    public function remove(string|array $values) : static
    {
        $this->{static::$property} = $this->app->array->remove($this->{static::$property}, $values);

        return $this;
    }
}
