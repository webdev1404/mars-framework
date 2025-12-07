<?php
/**
* The Config Container Class
* @package Mars
*/

namespace Mars\Config;

use Mars\Config\ArrayResult;

/**
 * The Config Container Class
 * Base class for configuration containers
 */
#[\AllowDynamicProperties]
class Container
{
    /**
     * Assigns data to the config object
     * @param array $data The data to assign
     */
    protected function assign(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof ArrayResult) {
                $this->$key = $value->values;
            } elseif (!is_array($value)) {
                $this->$key = $value;
            } else {
                $this->$key = new self;
                $this->$key->assign($value);
            }
        }
    }

    /**
     * @internal
     */
    public static function __set_state(array $data): self
    {
        $instance = new self;
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }
}