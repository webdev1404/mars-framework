<?php
/**
* The Early Hints Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
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
     * @var bool $cache_exists Whether the cache filename exists
     */
    protected ?bool $cache_exists = null;

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
        /*if ($this->app->protocol < 2) {
            return;
        }

        $headers = $this->app->cache->data->get('early-hints-headers');
        if (!$headers) {
            return;
        }

        $this->cache_exists = true;

        header('HTTP/' . $this->app->protocol . ' 103 Early Hints');
        foreach ($headers as $header) {
            header($header, false);
        }

        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();*/
    }

    /**
     * Caches the Early Hints headers for the current route
     */
    public function cache()
    {
        $this->cache_exists ??= $this->app->cache->data->has('early-hints-headers');
        /*if ($this->cache_exists) {
            return;
        }*/

        $headers = [
            ...$this->preload->getHeaders(),
            ...$this->preconnect->getHeaders(),
        ];

        $this->app->cache->data->set('early-hints-headers', $headers);
    }
}
