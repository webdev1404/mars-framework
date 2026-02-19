<?php
/**
* The Javascript Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Urls;

use Mars\Cache\Cache;
use Mars\Cache\Cacheable;

/**
 * The Javascript Cache Class
 * Class which handles the caching of javascript files
 */
class Javascript extends Asset
{
    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'js';

    /**
     * @see Cacheable::$extension
     * {@inheritDoc}
     */
    public protected(set) string $extension = 'js';

    /**
     * @see Cacheable::clean()
     * {@inheritDoc}
     */
    public function clean() : static
    {
        parent::clean();

        $this->app->cache->data->delete('js-version');

        return $this;
    }
}
