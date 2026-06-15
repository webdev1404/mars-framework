<?php
/**
* The Themes Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

/**
 * The Themes Cache Class
 * Class which handles the caching of theme files
 */
class Themes extends Extensions
{
    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_themes',      // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'extensions/themes';
}
