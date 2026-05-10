<?php
/**
* The Base Response Class
* @package Mars
*/

namespace Mars\Http\Response;

use Mars\App\Kernel;

/**
 * The Base Response Class
 * Base class for all response types
 */
abstract class Response
{
    use Kernel;

    /**
     * Outputs the content of the response, and caches it if caching is enabled
     * @param string $content The content to output
     */
    protected function outputContent(string $content)
    {
        if ($this->app->config->cache->page->enable) {
            $this->app->cache->pages->store($content);
        }

        echo $content;
    }
}
