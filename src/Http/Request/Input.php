<?php
/**
* The Base Input Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App\Kernel;

/**
 * The Base Input Class
 * Base class for the Request classes
 */
abstract class Input
{
    use Kernel;

    /**
     * The data to read from
     * @param array $data
     */
    public array $data = [];

    /**
     * Determines if a variable with this name is set
     * @param string $name The name of the  variable
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Returns the value of a variable
     * @param string $name The name of the variable
     * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
     * @param mixed $default_value The default value to return if the variable is not set
     * @param bool $is_array Whether the value should be returned as an array
     * @param bool $trim Whether to trim the value
     * @return mixed The value
     */
    public function get(string $name, mixed $default_value = '', string $filter = '', bool $is_array = false, bool $trim = true) : mixed
    {
        $value = $this->data[$name] ?? null;
        if (!$value) {
            $value = $default_value;
        }
        
        if ($is_array) {
            $value = (array)$value;
        }

        if ($trim) {
            $value = $this->app->filter->trim($value);
        }

        if ($filter) {
            $value = $this->app->filter->value($value, $filter);
        }

        return $value;
    }

    /**
     * Returns the value of a variable as an integer
     * @param string $name The name of the variable
     * @param int $default_value The default value to return if the variable is not set
     * @param bool $is_array Whether the value should be returned as an array
     * @return int|array The value
     */
    public function getInt(string $name, ?int $default_value = 0, bool $is_array = false) : null|int|array
    {
        return  $this->get($name, $default_value, 'int', $is_array);
    }

    /**
     * Returns the value of a variable as a float
     * @param string $name The name of the variable
     * @param float $default_value The default value to return if the variable is not set
     * @param bool $is_array Whether the value should be returned as an array
     * @return float|array The value
     */
    public function getFloat(string $name, ?float $default_value = 0, bool $is_array = false) : null|float|array
    {
        return  $this->get($name, $default_value, 'float', $is_array);
    }

    /**
     * Returns the value of a variable as an array
     * @param string $name The name of the variable
     * @param mixed $default_value The default value to return if the variable is not set
     * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
     * @param bool $trim Whether to trim the value
     * @return array The value
     */
    public function getArray(string $name, mixed $default_value = '', string $filter = '', bool $trim = true) : array
    {
        return $this->get($name, $default_value, $filter, true, $trim);
    }

    /**
     * Returns a key from an array
     * @param string $name The name of the array
     * @param string $key The key to retrieve
     * @param mixed $default_value The default value to return if the key is not found
     * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
     * @param bool $trim Whether to trim the value
     * @return mixed The value
     */
    public function getFromArray(string $name, string $key, mixed $default_value = '', string $filter = '', bool $trim = true) : mixed
    {
        $value = $this->data[$name][$key] ?? null;
        if (!$value) {
            $value = $default_value;
        }

        if ($trim) {
            $value = $this->app->filter->trim($value);
        }

        if ($filter) {
            $value = $this->app->filter->value($value, $filter);
        }

        return $value;
    }

    /**
     * Returns the value of a variable as an integer from an array
     * @param string $name The name of the array
     * @param string $key The key to retrieve
     * @param int $default_value The default value to return if the key is not found
     * @return int|null The value
     */
    public function getIntFromArray(string $name, string $key, ?int $default_value = 0) : null|int
    {
        return $this->getFromArray($name, $key, $default_value, 'int');
    }

    /**
     * Returns the value of a variable as a float from an array
     * @param string $name The name of the array
     * @param string $key The key to retrieve
     * @param float $default_value The default value to return if the key is not found
     * @return float|null The value
     */
    public function getFloatFromArray(string $name, string $key, ?float $default_value = 0) : null|float
    {
        return $this->getFromArray($name, $key, $default_value, 'float');
    }

    /**
     * Returns the raw value of a variable
     * @param string $name The name of the variable
     * @return mixed The value
     */
    public function getRaw(string $name) : mixed
    {
        return $this->data[$name] ?? '';
    }

    /**
     * Returns all the request data
     * @return array
     */
    public function getAll() : array
    {
        return $this->data;
    }

    /**
     * Sets the value of a variable
     * @param string $name The name of the variable
     * @param mixed $value The value
     * @return static
     */
    public function set(string $name, $value) : static
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Unsets a variable
     * @param string $name The name of the variable
     * @return static
     */
    public function unset(string $name) : static
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }

        return $this;
    }

    /**
     * For each element in the $data array, will check if a corresponding value with the same name exists in the post/get data.
     * If it does, will set the value in the $data to those values.
     * @param array|object $fill The data to be filled
     * @param array $filters Array listing the filters to apply to fields
     * @param array $array_fields Fields which can be filled with arrays
     * @param array $ignore_fields Fields to ignore when filling.
     * @param string $key_prefix Key prefix to use on the request data used when filling, if any
     * @return array|object Returns the filled $data
     */
    public function fill(array|object $fill, array $filters = [], array $array_fields = [], array $ignore_fields = [], string $key_prefix = '') : array|object
    {
        if (!$fill) {
            return $fill;
        }

        $is_array = is_array($fill);

        foreach ($fill as $key => $val) {
            $index = $key_prefix . $key;

            if (in_array($key, $ignore_fields)) {
                continue;
            }
            if (!isset($this->data[$index])) {
                continue;
            }

            $filter = $filters[$key] ?? '';

            $value = $value = $this->get($index, '', $filter, in_array($index, $array_fields));

            if ($is_array) {
                $fill[$key] = $value;
            } else {
                $fill->$key = $value;
            }
        }

        return $fill;
    }
}
