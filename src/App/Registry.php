<?php
/**
* The App Registry Trait
* @package Mars
*/

namespace Mars\App;

/**
 * The App Registry Trait
 * Trait adding the registry functionality to the App class
 */
trait Registry
{
    /**
     * @var array $data_array Array storing the data
     */
    protected array $data_array = [];

    /**
     * @var array $instances Array storing the instances
     */
    protected array $instances = [];

    /**
     * Sets a registry value
     * @param string $key The registry key
     * @param mixed $data The data
     * @return static
     */
    public function set(string $key, mixed $data) : static
    {
        $this->data_array[$key] = $data;

        return $this;
    }

    /**
     * Loads registry data from the map
     * @param array $map The map with the data to be loaded in the format key => value
     * @return static
     */
    public function load(array $map) : static
    {
        foreach ($map as $key => $data) {
            $this->data_array[$key] = $data;
        }

        return $this;
    }

    /**
     * Returns a registry value
     * @param string $key The registry key
     * @return mixed
     */
    public function get(string $key)
    {
        if (!isset($this->data_array[$key])) {
            return null;
        }

        if (is_callable($this->data_array[$key])) {
            if (isset($this->instances[$key])) {
                return $this->instances[$key];
            }

            $this->instances[$key] = $this->data_array[$key]($this);
            
            return $this->instances[$key];
        }

        return $this->data_array[$key];
    }

    /**
     * Magic method to get a registry value
     * @param string $key The registry key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Checks if a registry value exists
     * @param string $key The registry key
     * @return bool True, if the value exists, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->data_array[$key]);
    }

    /**
     * Deletes a registry value
     * @param string $key The registry key
     * @return $this
     */
    public function delete(string $key) : static
    {
        unset($this->data_array[$key]);
        unset($this->instances[$key]);

        return $this;
    }
}
