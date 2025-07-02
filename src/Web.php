<?php
/**
* The Web Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\Web\Request;

/**
 * The Web Class
 * Class handling http/web requests/responses
 */
class Web
{
    use Kernel;
    use LazyLoad;

    /**
     * @var Request $request The request object
     */
    #[LazyLoadProperty]
    public Request $request;

    /**
     * Builds the request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;
    }
}
