<?php
/**
* The App Kernel Trait
* @package Mars
*/

namespace Mars\App;

use Mars\App;
use Mars\HiddenProperty;

/**
 * The App Kernel Trait
 * Trait injecting/pulling the $app dependency into the current object
 */
trait Kernel
{
    use Info;

    /**
     * @var App $app The app object
     */
    #[HiddenProperty]
    protected ?App $app {
        get {
            if (isset($this->app)) {
                return $this->app;
            }

            $this->app = App::obj();

            return $this->app;
        }
    }

    /**
     * Builds the object
     * @param App $app The app object
     */
    public function __construct(?App $app = null)
    {
        if ($app) {
            $this->app = $app;
        }
    }

    /**
     * Unsets the app property when serializing
     */
    public function __sleep()
    {
        $data = get_object_vars($this);

        unset($data['app']);

        return array_keys($data);
    }

    /**
     * Sets the app property when unserializing
     */
    public function __wakeup()
    {
        $this->app = null;
    }
}
