<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Lazyload;
use Mars\Lazyload\GhostTrait;
use Mars\Lists\ListTrait;
use Mars\Response\Headers\EarlyHints;
use Mars\Response\Headers\CSP;

/**
 * The Headers Response Class
 * Handles the response headers
 */
class Headers
{
    use InstanceTrait;
    use ListTrait;
    use GhostTrait;

    /**
     * @var EarlyHints $early_hints The EarlyHints object
     */
    #[LazyLoad]
    public protected(set) EarlyHints $early_hints;

    /**
     * @var CSP $csp The CSP object
     */
    #[LazyLoad]
    public protected(set) CSP $csp;

    /**
     * Builds the Cookie Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;

        $this->list = $this->app->config->headers;
    }

    /**
     * Outputs the headers
     */
    public function output()
    {
        foreach ($this->list as $name => $value) {
            header("{$name}: $value");
        }
    }
}
