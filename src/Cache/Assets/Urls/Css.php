<?php
/**
* The Css Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Urls;

use Mars\Cache\Cache;
use Mars\Cache\Cacheable;

/**
 * The Css Cache Class
 * Class which handles the caching of css files
 */
class Css extends Asset
{
    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'css';

    /**
     * @see Cacheable::$extension
     * {@inheritDoc}
     */
    public protected(set) string $extension = 'css';
}
