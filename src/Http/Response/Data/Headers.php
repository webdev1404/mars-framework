<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\LazyLoadProperty;
use Mars\Data\MapTrait;
use Mars\Http\Response\Data\Headers\EarlyHints;
use Mars\Http\Response\Data\Headers\CSP;

/**
 * The Headers Response Class
 * Handles the response headers
 */
class Headers
{
    use Kernel;
    use Lazyload;
    use MapTrait;

    /**
     * @var EarlyHints $early_hints The EarlyHints object
     */
    #[LazyLoadProperty]
    public protected(set) EarlyHints $early_hints;

    /**
     * @var CSP $csp The CSP object
     */
    #[LazyLoadProperty]
    public protected(set) CSP $csp;

    /**
     * @var array $list The list of headers
     */
    protected array $list = [];
    
    /**
     * @internal
     */
    protected static string $property = 'list';

    /**
     * Builds the Cookie Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;

        $this->list = $this->app->config->http->response->headers->list;
    }

    /**
     * Outputs the headers
     */
    public function output()
    {
        if ($this->app->config->http->response->headers->csp->enable) {
            $this->csp->output();
        }

        foreach ($this->list as $name => $value) {
            header("{$name}: $value");
        }
    }
}
