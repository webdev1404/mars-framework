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
     * @var string $driver The used driver
     */
    protected string $driver_name = 'php';

    /**
     * @var string $dir The dir where the data will be cached
     */
    protected string $dir = 'config';

    /**
     * @var bool $can_hash Whether to hash the filename or not
     */
    protected bool $can_hash = false;

    /**
     * @var array $driver_params The parameters to pass to the driver constructor
     */
    protected array $driver_params = [true, 'cacheable_config'];
}
