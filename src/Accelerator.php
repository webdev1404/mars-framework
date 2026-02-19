<?php
/**
* The Accelerator Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Accelerators\AcceleratorInterface;

/**
 * The Accelerator Class
 * Handles the interactions with http accelerator - like varnish for example
 */
class Accelerator
{
    use Kernel;

    /**
     * @var array $drivers_list The supported drivers list
     */
    public protected(set) array $drivers_list = [
        'varnish' => \Mars\Accelerators\Varnish::class
    ];

    /**
     * @var bool $enabled Will be set to true, if enabled
     */
    public bool $enabled {
        get => $this->app->config->accelerator->enable;
    }

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->drivers_list, AcceleratorInterface::class, 'accelerators', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var AcceleratorInterface $driver The driver object
     */
    public protected(set) ?AcceleratorInterface $driver {
        get {
            if (!$this->enabled) {
                return null;
            }
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->accelerator->driver);

            return $this->driver;
        }
    }

    /**
     * Deletes $url from the accelerator's cache
     * @param string $url The url to delete
     * @return bool
     */
    public function delete(string $url) : bool
    {
        if (!$this->enabled) {
            return true;
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
            return true;
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
            return true;
        }

        return $this->driver->deleteAll();
    }
}
