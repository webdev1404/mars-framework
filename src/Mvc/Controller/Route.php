<?php
/**
* The Controller Route Class
* @package Mars
*/

namespace Mars\Mvc\Controller;

class Route
{
    /**
     * @var string $method The name of the method to call
     */
    public protected(set) string $method;

    /**
     * Builds the route object
     * @param string $method The name of the method to call
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }
}
