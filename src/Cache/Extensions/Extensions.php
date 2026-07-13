<?php
/**
* The Extensions Cache Class
* @package Mars
*/

namespace Mars\Cache\Extensions;

use Mars\Cache\Data;

/**
 * The Extensions Cache Class
 * Class which handles the caching of extension files
 */
class Extensions extends Data
{
    /**
     * @see \Mars\Cache\Cacheable::$driver_name
     * {@inheritDoc}
     */
    public string $driver_name {
        get => $this->app->config->cache->extensions->driver;
    }

    /**
     * @var string $type The type of extension
     */
    protected string $type = '';

    /**
     * Caches the extensions data
     */
    public function cache()
    {
        $this->driver->clearstat();

        $manager = $this->app->extensions->getManager($this->type);
        $manager->getAll(false);
        $manager->getEnabled(false);
    }
}
