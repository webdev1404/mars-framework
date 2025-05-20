<?php
/**
* The Cache Interface
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Cache Interface
 */
interface CacheInterface
{
    /**
     * Cleans all the cached data
     */
    public function clean();
}