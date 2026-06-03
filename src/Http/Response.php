<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\App\LazyLoadProperty;
use Mars\Http\Response\Body;
use Mars\Http\Response\Cookies;
use Mars\Http\Response\Headers;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
    use Kernel;
    use Lazyload;

    /**
     * @var Headers $headers The headers object
     */
    #[LazyLoadProperty]
    public Headers $headers;

    /**
     * @var Body $body The response body
     */
    #[LazyLoadProperty]
    public Body $body;

    /**
     * @var Cookies $cookies The cookies object
     */
    #[LazyLoadProperty]
    public Cookies $cookies;

    /**
     * Builds the Response object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($app);
    }

    /**
     * Sends the content as a response
     * @return string The sent content
     */
    public function send(Data $data) : string
    {
        $this->headers->send();

        return $this->body->send($data);
    }
}
