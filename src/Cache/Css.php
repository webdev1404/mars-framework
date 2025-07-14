<?php
/**
* The Css Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Css Cache Class
 * Class which handles the caching of css files
 */
class Css extends Cache
{
    /**
     * @var string $dir The dir where the css files will be cached
     */
    protected string $dir = 'css';
}
