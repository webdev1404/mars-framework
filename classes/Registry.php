<?php
/**
* The Registry Class
* @package Mars
*/

namespace Mars;

/**
 * The Registry Class
 * Stores/Retrives values
 */
class Registry
{
    use AppTrait;

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
    public function set($key, $data)
    {
        $this->data[$key] = $data;

        return $this;
    }

    /**
     * Returns a registry value
     * @return mixed
     */
    public function get($key)
    {
        if (is_callable($this->data[$key])) {
            if (isset($this->instances[$key])) {
                return $this->instances[$key];
            }

            $this->instances[$key] = $this->data[$key]($this->app);
            
            return $this->instances[$key];
        }

        return $this->data[$key] ?? null;
    }
}
