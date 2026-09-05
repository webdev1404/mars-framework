<?php
/**
* The Mapper Trait
* @package Mars
*/

namespace Mars\Data;

/**
 * The Mapper Trait
 */
trait MapperTrait
{
    /**
     * Maps a value or an array of values to a callback
     * If an array is given, the callback is applied to each element and an array of results is returned.
     * @param mixed $value The value or array of values to map
     * @param callable $callback The callback function
     * @return mixed The mapped value or array of mapped values
     */
    public function map(mixed $value, callable $callback) : mixed
    {
        if (is_array($value)) {
            return array_map($callback, $value);
        }

        return $callback($value);
    }
}
