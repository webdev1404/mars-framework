<?php
/**
* The Javascript Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Javascript Cache Class
 * Class which handles the caching of javascript files
 */
class Javascript extends Cache
{
    /**
     * @var string $dir The dir where the javascript files will be cached
     */
    protected string $dir = 'js';
}
