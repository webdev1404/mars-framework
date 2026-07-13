<?php
/**
* The Modules Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

/**
 * The Modules Cache Class
 * Class which handles the caching of module files
 */
class Modules extends Extensions
{
    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_modules',     // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'extensions/modules';

    /**
     * @see Extensions::$type
     * {@inheritDoc}
     */
    protected string $type = 'module';
}
