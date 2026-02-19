<?php
/**
* The App Kernel Trait
* @package Mars
*/

namespace Mars\App;

use Mars\App;

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
        $this->app = $app;
    }

    /**
     * Unsets the app property when serializing
     */
    public function __serialize(): array
    {
        $data = get_object_vars($this);

        unset($data['app']);

        return $data;
    }

    /**
     * Sets the app property when unserializing
     * @param array $data The data to unserialize
     */
    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $this->app = App::obj();
    }
}
