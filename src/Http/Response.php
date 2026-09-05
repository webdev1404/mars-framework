<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Http\Response\Body;
use Mars\Http\Response\Cookie;
use Mars\Http\Response\Headers;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
    use Kernel;
    use LazyLoad;

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
     * @var Cookie $cookie The cookie object
     */
    #[LazyLoadProperty]
    public Cookie $cookie;

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

    /**
     * Redirects the user to a given URL
     */
    public function redirect(?string $url = null)
    {
        $url = $url ? $this->app->url->get($url) : $this->app->url->root;

        $this->body->redirect($url);
    }
}
