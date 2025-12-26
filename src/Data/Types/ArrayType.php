<?php
/**
* The Array Class
* @package Mars
*/

namespace Mars\Data\Types;

/**
 * The Array Class
 * Handles array operations
 */
class ArrayType
{
    /**
     * Returns an array from an array/object/iterator
     * @param mixed $array The array
     * @return array
     */
    public function get(mixed $array) : array
    {
        if (!$array) {
            return [];
        }

        if (is_array($array)) {
            return $array;
        } elseif (is_iterable($array)) {
            return iterator_to_array($array);
        } elseif (is_object($array)) {
            return get_object_vars($array);
        } else {
            return (array)$array;
        }
    }

    /**
     * Flips an array, so the values become the keys, with the new values being set to $value
     * @param array $array The array to flip
     * @param mixed $value The value to set for the new keys
     * @return array The flipped array
     */
    public static function flip(array $array, mixed $value = true) : array
    {
        if (!$array) {
            return [];
        }
        
        return array_combine($array, array_fill(0, count($array), $value));
    }

    /**
     * Unsets from $array the specified keys
     * @param array $array The array
     * @param string|array $keys The keys to unset
     * @return array The array
     */
    public static function unset(array $array, string|array $keys) : array
    {
        $keys = (array)$keys;

        foreach ($keys as $key) {
            if (isset($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Removes the specified values from the array
     * @param array $array The array
     * @param string|array $values The values to remove
     * @return array The array
     */
    public static function remove(array $array, string|array $values) : array
    {
        $values = (array)$values;

        return array_diff($array, $values);
    }

    /**
     * Builds a nested tree from a flat array
     * @param array $data The flat array
     * @param string $separator The separator used in the keys
     * @return array The tree
     */
    public function getTree(array $data, string $separator = '.'): array
    {
        $tree = [];

        foreach ($data as $key => $value) {
            $parts = explode($separator, $key);
            $ref = &$tree;

            foreach ($parts as $part) {
                $ref[$part] ??= [];
                $ref = &$ref[$part];
            }

            $ref = $value;
            unset($ref);
        }

        return $tree;
    }
}
