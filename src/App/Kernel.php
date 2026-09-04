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
    use Serialize;

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
}
