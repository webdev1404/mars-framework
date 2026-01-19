<?php
/**
* The Entities Trait
* @package Mars
*/

namespace Mars\Objects;

use Mars\Entity;

/**
 * The Entities Trait
 * Container of multiple objects
 */
trait EntitiesTrait
{
    /**
     * @var array $data Array containing the objects
     */
    protected array $data = [];

    /**
     * Builds the objects
     * @param array|object $data The data to load the objects from
     */
    public function __construct(array|object $data = [])
    {
        $this->set($data);
    }

    /**
     * Returns the class name of the objects
     * @return string The class name
     */
    public function getClass() : string
    {
        return static::$class ?? '\StdClass';
    }

    /**
     * Returns the number of loaded objects
     * @return int
     */
    public function count() : int
    {
        return count($this->data);
    }

    /**
     * Determines if there are loaded objects
     * @return bool
     */
    public function has() : bool
    {
        if ($this->data) {
            return true;
        }

        return false;
    }

    /**
     * Sets the data/objects
     * @param array|object $data The entities array
     * @return static
     */
    public function set(array|object $data) : static
    {
        $this->data = [];

        return $this->add($data);
    }

    /**
     * Adds $data to the existing data/objects
     * @param array|object $data The data to add
     * @return static
     */
    public function add(array|object $data) : static
    {
        if (is_object($data)) {
            $data = [$data];
        }

        foreach ($data as $properties) {
            $obj = $this->getObject($properties);

            $id = null;
            if ($obj instanceof Entity) {
                $id = $obj->getId();
            }

            if ($id) {
                $this->data[$id] = $obj;
            } else {
                $this->data[] = $obj;
            }
        }

        return $this;
    }

    /**
     * Updates an object from the collection
     * @param int $id The index of the object to update
     * @param array|object $data The object's data
     * @return bool Returns true if the data was updated, false if the index wasn't found
     */
    public function update(int $id, array|object $data) : bool
    {
        if (isset($this->data[$id])) {
            $this->data[$id] = $this->getObject($data);

            return true;
        }

        return false;
    }

    /**
     * Returns the data/objects
     * @param int $id The id of the object to return. If null, all the objects are returned
     * @return mixed
     */
    public function get(?int $id = null)
    {
        if ($id === null) {
            return $this->data;
        }

        return $this->data[$id] ?? null;
    }

    /**
     * Builds an object of $this->class from $data
     * @param array|object $data The data
     */
    public function getObject(array|object $data) : Entity
    {
        if ($data instanceof Entity) {
            return $data;
        }

        $class_name = $this->getClass();
        return new $class_name($data);
    }

    /**
     * Returns a field from the loaded items as an array
     * @param string $field The name of the field
     * @param string $index The name of the index field, if any
     * @return array
     */
    public function getCol(string $field, ?string $index = null) : array
    {
        return array_column($this->data, $field, $index);
    }

    /**
     * Returns the iterator
     * @return \Traversable
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
