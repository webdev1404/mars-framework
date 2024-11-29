<?php
/**
* The Http Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Lazyload\GhostTrait;
use Mars\Http\Request;

/**
 * The Http Class
 * Class handling http requests/responses
 */
class Http
{
    use InstanceTrait;
    use GhostTrait;

    /**
     * @var Request $request The request object
     */
    #[LazyLoad]
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
