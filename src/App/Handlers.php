<?php
/**
* The Handlers Class
* @package Mars
*/

namespace Mars\App;

use Mars\App;
use Mars\App\Kernel;
use Mars\Data\MapTrait;

/**
 * The Handlers Class
 * Encapsulates a list of suported handlers
 */
class Handlers
{
    use Kernel;
    use MapTrait {
        add as protected addToMap;
        remove as protected removeFromMap;
    }

    /**
     * @var bool $store If true, the handlers will be stored in $this->objects
     */
    public bool $store = true;

    /**
     * @var string $interface_name The interface the driver must implement, if any
     */
    public ?string $interface_name = '';

    /**
     * @var array $list The list of handlers
     */
    protected array $list = [];

    /**
     * @var array $objects Array storing the stored objects, if $store is true
     */
    protected array $objects = [];

    /**
     * @internal
     */
    protected static string $property = 'list';

    /**
     * Builds the handler object
     * @param array $list The list of supported handlers
     * @param string $interface_name The interface the handlers must implement, if any
     * @param App $app The app object
     */
    public function __construct(array $list, ?string $interface_name = null, ?App $app = null)
    {
        $this->interface_name = $interface_name;
        $this->list = $list;
        $this->app = $app;
    }

    /**
     * Adds an object to the list
     * @param string $name The name of the object
     * @param mixed $class The class of the object, or a callable
     */
    public function add(string $name, mixed $class) : static
    {
        if ($this->store && isset($this->objects[$name])) {
            unset($this->objects[$name]);
        }

        $this->addToMap($name, $class);

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
