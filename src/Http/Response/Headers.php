<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Http\Response;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\App\LazyLoadProperty;
use Mars\Data\MapTrait;
use Mars\Http\Response\Headers\EarlyHints;
use Mars\Http\Response\Headers\CSP;

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
     * Builds the Headers Response object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($app);

        $this->list = $this->app->config->headers->list;
    }

    /**
     * Sends the headers
     */
    public function send()
    {
        if ($this->app->config->headers->csp->enable) {
            $this->csp->register();
        }

        $this->list = $this->app->plugins->filter('response.headers.list', $this->list, $this);

        $this->app->plugins->run('response.headers.send', $this->list, $this);

        foreach ($this->list as $name => $value) {
            header("{$name}: $value");
        }

        $this->app->plugins->run('response.headers.sent', $this->list, $this);
    }
}
