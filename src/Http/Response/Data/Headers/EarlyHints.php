<?php
/**
* The Early Hints Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\LazyLoadProperty;
use Mars\Http\Response\Data\Headers\EarlyHints\Preload;
use Mars\Http\Response\Data\Headers\EarlyHints\Preconnect;

/**
 * The Early Hints Header Response Class
 * Handles the 103 Early Hints header
 */
class EarlyHints
{
    use Kernel;
    use LazyLoad;
    
    /**
     * @var Preload $preload The Preload object
     */
    #[LazyLoadProperty]
    public protected(set) Preload $preload;

    /**
     * @var Preconnect $preconnect The Preconnect object
     */
    #[LazyLoadProperty]
    public protected(set) Preconnect $preconnect;
    
    /**
     * Builds the Early Hints object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);
        
        $this->app = $app;
    }

    /**
     * Sends the Early Hints header
     */
    public function output()
    {
        //do not send early hints if we are not using http2 or above
        if ($this->app->protocol < 2) {
            return;
        }

        $this->preload->set($this->app->config->hints->early_hints->list['preload'] ?? []);
        $this->preconnect->set($this->app->config->hints->early_hints->list['preconnect'] ?? []);

        if ($this->preload->count() || $this->preconnect->count()) {
            header('HTTP/' . $this->app->protocol . ' 103 Early Hints');

            $this->preload->send();
            $this->preconnect->send();

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }
    }
}
