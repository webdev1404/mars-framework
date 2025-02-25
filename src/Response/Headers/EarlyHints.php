<?php
/**
* The Early Hints Header Response Class
* @package Mars
*/

namespace Mars\Response\Headers;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\LazyLoad;
use Mars\Lazyload\GhostTrait;
use Mars\Response\Headers\EarlyHints\Preload;
use Mars\Response\Headers\EarlyHints\Preconnect;

/**
 * The Early Hints Header Response Class
 * Handles the 103 Early Hints header
 */
class EarlyHints
{
    use InstanceTrait;
    use GhostTrait;
    
    /**
     * @var Preload $preload The Preload object
     */
    #[LazyLoad]
    public protected(set) Preload $preload;

    /**
     * @var Preconnect $preconnect The Preconnect object
     */
    #[LazyLoad]
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

        $this->preload->set($this->app->config->early_hints_headers['preload'] ?? []);
        $this->preconnect->set($this->app->config->early_hints_headers['preconnect'] ?? []);

        if ($this->preload->count() || $this->preconnect->count()) {
            header("HTTP/1.1 103 Early Hints");

            $this->preload->send();
            $this->preconnect->send();

            flush();
        }
    }
}
