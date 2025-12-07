<?php
/**
* The Data Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Data Cache Class
 * Class which handles the caching of data
 */
class Data extends Cacheable
{
    /**
     * @var string $driver The used driver
     */
    protected string $driver_name {
        get => $this->app->config->cache->data_driver ?? $this->app->config->cache->driver;
    }

    /**
     * @var string $dir The dir where the data will be cached
     */
    protected string $dir = 'data';

    /**
     * @var bool $can_hash Whether to hash the filename or not
     */
    protected bool $can_hash = false;

    /**
     * @var array $driver_params The parameters to pass to the driver constructor
     */
    protected array $driver_params = [true, 'cacheable_data'];
}
