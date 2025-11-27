<?php
/**
* The Routes Container Class
* @package Mars
*/

namespace Mars\Router\Loaders;

/**
 * The Routes Container Class
 * Contains the loaded routes information
 */
class Routes
{
    /**
     * @var array $hashes The defined routes hashes
     */
    public array $hashes = [];

    /**
     * @var array $data The route's data
     */
    public array $data = [];

    /**
     * @var array $names The defined routes names
     */
    public array $names = [];

    /**
     * Returns the route key for a route
     * @param array $route The route data
     * @return int The route key
     */
    public function getKey(array $route) : int
    {
        $key = array_find_key($this->data, function($value) use ($route) {
            return $value == $route;
        });

        if ($key !== null) {
            return $key;
        }

        $this->data[] = $route;
        
        return array_key_last($this->data);
    }

    /**
     * Resets the routes data
     * @return static
     */
    public function reset() : static
    {
        $this->hashes = [];
        $this->data = [];
        $this->names = [];

        return $this;
    }
}
