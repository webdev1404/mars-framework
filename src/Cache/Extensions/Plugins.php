<?php
/**
* The Plugins Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

/**
 * The Plugins Cache Class
 * Class which handles the caching of plugin files
 */
class Plugins extends Extensions
{
    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_plugins',     // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'extensions/plugins';

    /**
     * @see Extensions::$type
     * {@inheritDoc}
     */
    protected string $type = 'plugin';
}
