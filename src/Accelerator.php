<?php
/**
* The Accelerator Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Accelerators\DriverInterface;

/**
 * The Accelerator Class
 * Handles the interactions with http accelerator - like varnish for example
 */
class Accelerator
{
    use InstanceTrait;

    /**
     * @var bool $enabled Will be set to true, if enabled
     */
    public bool $enabled {
        get => $this->app->config->accelerator_enable;
    }

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'accelerators', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    protected ?DriverInterface $driver {
        get {
            if (!$this->enabled) {
                return null;
            }            
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->accelerator_driver);

            return $this->driver;
        }
    }

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'varnish' => \Mars\Accelerators\Varnish::class
    ];

    /**
     * Deletes $url from the accelerator's cache
     * @param string $url The url to delete
     * @return bool
     */
    public function delete(string $url) : bool
    {
        if (!$this->enabled) {
            return $this;
        }

        return $this->driver->delete($url);
    }

    /**
     * Deletes by pattern from the accelerator's cache
     * @param string $pattern The pattern
     * @return bool
     */
    public function deleteByPattern(string $pattern) : bool
    {
        if (!$this->enabled) {
            return $this;
        }

        return $this->driver->deleteByPattern($pattern);
    }

    /**
     * Deletes all the data from the accelerator's cache
     * @return bool
     */
    public function deleteAll() : bool
    {
        if (!$this->enabled) {
            return $this;
        }

        return $this->driver->deleteAll();
    }
}
