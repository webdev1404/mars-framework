<?php
/**
* The Http Class
* @package Mars
*/

namespace Mars;

use Mars\Http\Request;

/**
 * The Http Class
 * Class handling http requests/responses
 */
class Http
{
    use AppTrait;

    /**
     * @var Request $request The request object
     */
    public Request $request;

    /**
     * Builds the request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->request = new Request($app);
    }
}
