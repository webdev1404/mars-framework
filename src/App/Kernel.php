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
    protected App $app;

    /**
     * Builds the object
     * @param App $app The app object
     */
    public function __construct(?App $app = null)
    {
        $this->app = $app ?? static::getApp();
    }

    /**
     * Returns the app object
     * @return App The app object
     */
    public static function getApp(): App
    {
        static $app = null;
        if ($app === null) {
            $app = App::obj();
        }

        return $app;
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
        $this->app = App::obj();
    }
}
