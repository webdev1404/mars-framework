<?php
/**
* The Javascript Assets List Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Lists;

use Mars\Cache\Cache;

/**
 * The Javascript Assets List Cache Class
 * Class which handles the caching of Javascript asset lists
 */
class Javascript extends Assets
{
    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'assets/js';
}
