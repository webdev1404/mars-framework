<?php
/**
* The Data Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Data Class
 * Handles data operations
 */
class Data
{
    use Kernel;

    /**
     * Determines if the property exists
     * @param array|object $data The data to return the property from
     * @param string $name The name of the property/index
     * @return bool True if the property exists
     */
    public function hasProperty(array|object $data, string $name) : bool
    {
        if (is_array($data)) {
            return isset($data[$name]);
        } else {
            return isset($data->$name);
        }
    }

    /**
     * Returns a property of an object or an array value
     * @param array|object $data The data to return the property from
     * @param string $name The name of the property/index
     * @return mixed The property
     */
    public function getProperty(array|object $data, string $name)
    {
        if (is_array($data)) {
            return $data[$name] ?? null;
        } else {
            return $data->$name ?? null;
        }
    }

    /**
     * Returns a list of properties of an object or an array value
     * @param array|object $data The data to return the property from
     * @param array $properties The name of the properties to return
     * @return array The properties
     */
    public function getProperties(array|object $data, array $properties = []) : array
    {
        $properties_array = [];
        if ($properties) {
            foreach ($properties as $name) {
                if ($this->hasProperty($data, $name)) {
                    $properties_array[$name] = $this->getProperty($data, $name);
                }
            }
        } else {
            $properties_array = (array)$data;
        }

        return $properties_array;
    }

    /**
     * Maps a value [scalar|array] to a callback
     * @param mixed $value The value
     * @param callable $callback The callback function
     */
    public function map($value, callable $callback)
    {
        if (is_array($value)) {
            return array_map($callback, $value);
        }

        return $callback($value);
    }
}
