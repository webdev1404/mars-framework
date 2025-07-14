<?php
/**
* The Object Class
* @package Mars
*/

namespace Mars\Data\Types;

use Mars\App\Kernel;

/**
 * The Object Class
 * Handles object operations
 */
class ObjectType
{
    use Kernel;

    /**
     * Returns an object from an class/callable...
     * @param mixed $class The class/callable etc..
     * @param mixed $args The arguments to pass to the constructor
     * @return object
     */
    public function get(mixed $class, ...$args) : object
    {
        $args[] = $this->app;

        $object = null;
        if (is_string($class)) {
            $object = new $class(...$args);
        } elseif (is_callable($class)) {
            $object = $class($args);
        } else {
            $object = (object)$class;
        }

        return $object;
    }

    /**
     * Returns the properties of an object
     * @param object $object The object
     * @return array The properties
     */
    public function getProperties(object $object) : array
    {
        return get_object_vars($object);
    }
}
