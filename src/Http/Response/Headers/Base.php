<?php
/**
* The Base Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Headers;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;

/**
 * The Base Header Response Class
 * Base class for all header response classes
 */
abstract class Base
{
    use Kernel;
    use LazyLoad;

    /**
     * Builds the Base Header object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($app);
    }
}
