<?php
/**
* The Cookies Request Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App;

/**
 * The Cookies Request Class
 * Handles the $_COOKIE interactions
 */
class Cookies extends Input
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
    public function get(string $name, mixed $default_value = '', string $filter = '', bool $is_array = false, bool $trim = true, bool $decode = true) : mixed
    {
        if ($decode) {
            $value = $this->data[$name] ?? '';

            if ($value) {
                return $this->app->json->decode($value);
            }
        }

        return parent::get($name, $default_value, $filter, $is_array, $trim);
    }
}
