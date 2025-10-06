<?php
/**
* The Objects List Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Objects List Trait
 * Encapsulates a list of objects
 * The 'protected static string $property' property must be defined in the class using this trait to specify the property that holds the list.
 */
trait ObjectsTrait
{
    use MapTrait {
        set as protected setInMap;
        remove as protected removeFromMap;
    }

    /**
     * @var bool $store If true, the handlers will be stored in $this->objects
     */
    public bool $store = true;

    /**
     * @var array $objects Array storing the stored objects, if $store is true
     */
    protected array $objects = [];

    /**
     * Adds an object to the list
     * @param string $name The name of the object
     * @param mixed $class The class of the object, or a callable
     */
    public function add(string $name, mixed $class) : static
    {
        return $this->set($name, $class);
    }

    /**
     * Adds an object to the list
     * @param string $name The name of the object
     * @param mixed $class The class of the object, or a callable
     */
    public function set(string $name, mixed $class) : static
    {
        if ($this->store && isset($this->objects[$name])) {
            unset($this->objects[$name]);
        }

        $this->setInMap($name, $class);

        return $this;
    }

    /**
     * Returns the handler
     * @param string $name The name of the handler
     * @param mixed $args Arguments to pass to the handler's constructor
     * @return object The object
     */
    public function get(string $name, ...$args) : object
    {
        if ($this->store && isset($this->objects[$name])) {
            return $this->objects[$name];
        }
        if (!isset($this->{static::$property}[$name])) {
            throw new \Exception("Unknown object '{$name}'");
        }
  
        $object = $this->app->object->get($this->{static::$property}[$name], ...$args);

        if ($this->interface_name) {
            if (!$object instanceof $this->interface_name) {
                throw new \Exception("Object {$class} must implement interface {$this->interface_name}");
            }
        }

        if ($this->store) {
            $this->objects[$name] = $object;
        }

        return $object;
    }

    /**
     * Returns all the objects
     * @return array
     */
    public function getAll() : array
    {
        if ($this->store && $this->objects) {
            return $this->objects;
        }

        foreach ($this->{static::$property} as $name => $class) {
            $this->get($name);
        }

        return $this->objects;
    }

    /**
     * @see MapTrait::remove()
     * {@inheritdoc}
     */
    public function remove(string $name) : static
    {
        if ($this->store && isset($this->objects[$name])) {
            unset($this->objects[$name]);
        }

        return $this->removeFromMap($name);
    }
}
