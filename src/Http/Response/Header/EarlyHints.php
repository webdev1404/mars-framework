<?php
/**
* The Early Hints Header Response Class
* @package Mars
*/

namespace Mars\Http\Response\Header;

use Mars\App\LazyLoadProperty;
use Mars\Http\Response\Header\EarlyHints\Preload;
use Mars\Http\Response\Header\EarlyHints\Preconnect;

/**
 * The Early Hints Header Response Class
 * Handles the 103 Early Hints header
 */
class EarlyHints extends Base
{
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
     * Sends the Early Hints header
     */
    public function send()
    {
        //do not send early hints if we are not using http2 or above
        if ($this->app->protocol < 2) {
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
        flush();
    }

    /**
     * Caches the Early Hints headers for the current route
     */
    public function cache()
    {
        $this->cache_exists ??= $this->app->cache->data->has('early-hints-headers');
        if ($this->cache_exists) {
            return;
        }

        $headers = [
            ...$this->preload->getHeaders(),
            ...$this->preconnect->getHeaders(),
        ];

        $this->app->cache->data->set('early-hints-headers', $headers);
    }
}
