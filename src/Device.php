<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Device\DriverInterface;

/**
 * The Device Class
 * Encapsulates the user's device
 */
class Device
{
    use InstanceTrait;

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'device', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var string $device The device type. Eg: desktop/tablet/smartphone
     */
    public protected(set) string $device {
        get {
            if (isset($this->device)) {
                return $this->device;
            }

            $this->device = '';
            if (!$this->app->config->device_start || $this->app->is_bin) {
                return $this->device;
            }
            if ($this->app->config->development_device) {
                $this->device = $this->app->config->development_device;
                return $this->device;
            }

            $device = $this->app->session->get('device');
            if ($device !== null) {
                $this->device = $device;
                return $this->device;
            }

            //do we get the device name from varnish?
            if (isset($_SERVER['X-Device'])) {
                if (in_array($_SERVER['X-Device'], $this->devices)) {
                    $this->device = $_SERVER['X-Device'];
                }
            }

            if (!$this->device) {
                $driver = $this->drivers->get($this->app->config->device_driver);
                $this->device = $driver->get();
            }

            $this->app->session->set('device', $this->device);

            return $this->device;
        }
    }

    /**
     * @var string $devices Array listing the supported devices
     */
    public protected(set) array $devices = ['desktop', 'tablet', 'smartphone'];

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'mobile_detect' => \Mars\Device\MobileDetect::class
    ];

    /**
     * Returns the current device
     * @return string The device
     */
    public function get() : string
    {
        return $this->device;
    }

    /**
     * Returns true if the user is using a desktop
     * @return bool
     */
    public function isDesktop() : bool
    {
        if (!$this->device || $this->device == 'desktop') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the user is using a tablet/smartphone
     * @return bool
     */
    public function isMobile() : bool
    {
        if ($this->device == 'tablet' || $this->device == 'smartphone') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the user is using a tablet
     * @return bool
     */
    public function isTablet() : bool
    {
        if ($this->device == 'tablet') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the user is using a smartphone
     * @return bool
     */
    public function isSmartphone() : bool
    {
        if ($this->device == 'smartphone') {
            return true;
        }

        return false;
    }
}
