<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Devices\DeviceInterface;

/**
 * The Device Class
 * Encapsulates the user's device
 */
class Device
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'mobile_detect' => \Mars\Devices\MobileDetect::class
    ];

    /**
     * @var array $devices Array listing the supported devices
     */
    public protected(set) array $devices = ['desktop', 'tablet', 'smartphone'];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DeviceInterface::class, 'device', $this->app);

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

            $this->device = 'desktop';
            if ($this->app->is_cli) {
                return $this->device;
            }
            if ($this->app->config->development->device) {
                $this->device = $this->app->config->development->device;
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
                $driver = $this->drivers->get($this->app->config->device->driver);
                $this->device = $driver->get();
            }

            $this->app->session->set('device', $this->device);

            return $this->device;
        }
    }

    /**
     * @var bool $is_desktop If true, the user is using a desktop
     */
    public protected(set) bool $is_desktop {
        get {
            if (isset($this->is_desktop)) {
                return $this->is_desktop;
            }

            $this->is_desktop = $this->device == 'desktop';

            return $this->is_desktop;
        }
    }

    /**
     * @var bool $is_mobile If true, the user is using a mobile device
     */
    public protected(set) bool $is_mobile {
        get {
            if (isset($this->is_mobile)) {
                return $this->is_mobile;
            }

            $this->is_mobile = $this->device == 'tablet' || $this->device == 'smartphone';

            return $this->is_mobile;
        }
    }

    /**
     * @var bool $is_tablet If true, the user is using a tablet
     */
    public protected(set) bool $is_tablet {
        get {
            if (isset($this->is_tablet)) {
                return $this->is_tablet;
            }

            $this->is_tablet = $this->device == 'tablet';

            return $this->is_tablet;
        }
    }

    /**
     * @var bool $is_smartphone If true, the user is using a smartphone
     */
    public protected(set) bool $is_smartphone {
        get {
            if (isset($this->is_smartphone)) {
                return $this->is_smartphone;
            }

            $this->is_smartphone = $this->device == 'smartphone';

            return $this->is_smartphone;
        }
    }
}
