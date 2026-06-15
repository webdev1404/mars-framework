<?php
/**
* The Languages Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

/**
 * The Languages Cache Class
 * Class which handles the caching of language files
 */
class Languages extends Extensions
{
    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_languages',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'extensions/languages';
}
