<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\App\LazyLoadProperty;
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
     * Builds the Headers Response object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;

        $this->list = $this->app->config->headers->list;
    }

    /**
     * Collects the headers to be sent
     */
    protected function collect()
    {
        if ($this->app->config->headers->csp->enable) {
            $this->csp->collect();
        }

        $this->app->plugins->run('response.headers.collect', $this->list, $this);
    }

    /**
     * Outputs the headers
     */
    public function output()
    {
        $this->collect();

        $this->list = $this->app->plugins->filter('response.headers.list', $this->list, $this);

        foreach ($this->list as $name => $value) {
            header("{$name}: $value");
        }
    }
}
