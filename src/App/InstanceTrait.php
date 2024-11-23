<?php
/**
* The App Instance Trait
* @package Mars
*/

namespace Mars\App;

use Mars\App;
use Mars\Hidden;
use Mars\Debug\InfoTrait;

/**
 * The App Instance Trait
 * Trait injecting/pulling the $app dependency into the current object
 */
trait InstanceTrait
{
    use InfoTrait;

    /**
     * @var App $app The app object
     */
    #[Hidden]
    protected App $app;

    /**
     * Builds the object
     * @param App $app The app object
     */
    public function __construct(?App $app = null)
    {
        $this->app = $app ?? App::get();
    }

    /**
     * Returns the app object
     * @return App
     */
    public function getApp() : App
    {
        return $this->app ?? App::get();
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
        $this->app = App::get();
    }
}
