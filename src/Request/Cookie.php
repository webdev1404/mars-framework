<?php
/**
* The COOKIE Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The COOKIE Request Class
 * Handles the $_COOKIE interactions
 */
class Cookie extends Input
{
    /**
     * Builds the Cookie Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_COOKIE;
    }

    /**
     * Returns the value of a variable
     * @param string $name The name of the variable
     * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
     * @param bool $is_array If true, will force the returned value to an array
     * @param bool $decode If true, will decode the value
     * @return mixed The value
     */
    public function get(string $name, string $filter = '', mixed $default_value = '', bool $is_array = false, bool $decode = true) : mixed
    {
        if ($decode) {
            $value = $this->data[$name] ?? '';

            if ($value) {
                return $this->app->json->decode($value);
            }
        }

        return parent::get($name, $filter, $default_value, $is_array);
    }
}
