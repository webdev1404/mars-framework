<?php
/**
* The App Utils Trait
* @package Mars
*/

namespace Mars\App;

/**
 * The App Utils Trait
 * Trait containing utility methods
 */
trait UtilsTrait
{
    /**
     * Returns a html escaped language string
     * @param string $str The string index as defined in the languages file
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @return string The language string
     */
    public static function __(string $str, array $replace = []) : string
    {
        $str = static::$instance->lang->strings[$str] ?? $str;

        if ($replace) {
            $str = str_replace(array_keys($replace), $replace, $str);
        }

        return static::$instance->escape->html($str);
    }

    /**
     * Html escapes a string. Shorthand for $app->escape->html($value)
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public static function e(string $value) : string
    {
        return static::$instance->escape->html($value);
    }

    /**
     * Converts a string to a class name. Eg: some-action => SomeAction
     * @param string $str The string to convert
     * @return string The class name
     */
    public static function getClass(string $str) : string
    {
        $str = preg_replace('/[^a-z0-9\- ]/i', '', $str);
        $str = str_replace(' ', '-', $str);

        $str = ucwords($str, '-');
        $str = str_replace('-', '', $str);

        return $str;
    }

    /**
     * Converts a string to a method name. Eg: some-action => someAction
     * @param string $str The string to convert
     * @return string The method name
     */
    public static function getMethod(string $str) : string
    {
        $str = preg_replace('/[^a-z0-9\-_ ]/i', '', $str);
        $str = str_replace('_', '-', $str);
        $str = str_replace(' ', '-', $str);

        $str = ucwords($str, '-');
        $str = lcfirst($str);
        $str = str_replace('-', '', $str);

        return $str;
    }

    /**
     * Determines if the property exists
     * @param array|object $data The data to return the property from
     * @param string $name The name of the property/index
     * @return mixed The property
     */
    public static function hasProperty(array|object $data, string $name) : bool
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
    public static function getProperty(array|object $data, string $name)
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
    public static function getProperties(array|object $data, array $properties = []) : array
    {
        $properties_array = [];

        if ($properties) {
            foreach ($properties as $name) {
                if (static::hasProperty($data, $name)) {
                    $properties_array[$name] = static::getProperty($data, $name);
                }
            }
        } else {
            $properties_array = (array)$data;
        }

        return $properties_array;
    }

    /**
     * Removes the specified properties from the data
     * @param array $properties The name of the properties to remove
     * @return array The properties
     */
    public static function filterProperties(array|object $data, array $properties) : array
    {
        return static::unset((array)$data, $properties);
    }

    /**
     * Returns an array from an array/object/iterator
     * @param mixed $array The array
     * @return array
     */
    public static function array($array) : array
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
     * Unsets from $array the specified keys
     * @param array $array The array
     * @param string|array The keys to unset
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

    /********************** DEBUG FUNCTIONS ***************************************/

    /**
     * Does a print_r on $var and outputs <pre> tags
     * @param mixed $var The variable
     * @param bool $die If true, will call die after
     */
    public static function pp($var, bool $die = true)
    {
        echo '<pre>';
        \print_r($var);
        echo '</pre>';

        if ($die) {
            die;
        }
    }

    /**
     * Alias for dd
     * @see App::pp()
     */
    public static function dd($var, bool $die = true)
    {
        static::pp($var, $die);
    }

    /**
     * Prints the debug backtrace
     */
    public static function backtrace()
    {
        echo '<pre>';
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo '</pre>';

        die;
    }
}
