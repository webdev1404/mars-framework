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
     * @var array $data Array storing the data
     */
    protected array $data = [];

    /**
     * @var array $instances Array storing the instances
     */
    protected array $instances = [];

    /**
     * Sets a registry value
     * @param string $key The registry key
     * @param mixed $data The data
     * @return $this
     */
    public function set(string $key, $data)
    {
        $this->data[$key] = $data;

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
            $this->data[$key] = $data;
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
        if (!isset($this->data[$key])) {
            return null;
        }

        if (is_callable($this->data[$key])) {
            if (isset($this->instances[$key])) {
                return $this->instances[$key];
            }

            $this->instances[$key] = $this->data[$key](static::$instance);
            
            return $this->instances[$key];
        }

        return $this->data[$key];
    }

    /**
     * Magic method to get a registry value
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
        return isset($this->data[$key]);
    }

    /**
     * Deletes a registry value
     * @param string $key The registry key
     * @return $this
     */
    public function delete(string $key) : static
    {
        unset($this->data[$key]);
        unset($this->instances[$key]);

        return $this;
    }
}
