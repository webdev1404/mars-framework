<?php
/**
* The CSS Assets List Cache Class
* @package Mars
*/

namespace Mars\Cache\Assets\Lists;

use Mars\Cache\Cache;

/**
 * The CSS Assets List Cache Class
 * Class which handles the caching of CSS asset lists
 */
class Css extends Assets
{
    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'assets/css';
}
