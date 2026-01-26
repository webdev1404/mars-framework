<?php
/**
* The Config Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Config Cache Class
 * Class which handles the caching of config data
 */
class Config extends Data
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name = 'php';

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,              // use files cache
        'cacheable_config', // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'config';
}
