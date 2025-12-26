<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Devices\DeviceInterface;
use Mars\Devices\Type;

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
     * @var Type $device The device type. Eg: desktop/tablet/smartphone
     */
    public protected(set) Type $device {
        get {
            if (isset($this->device)) {
                return $this->device;
            }

            $this->device = Type::Desktop;
            if ($this->app->is_cli) {
                return $this->device;
            }

            if ($this->app->config->development->device) {
                $this->device = Type::from($this->app->config->development->device);

                return $this->device;
            }

            $session_device = $this->app->session->get('device');
            if ($session_device) {
                $this->device = Type::tryFrom($session_device) ?? Type::Desktop;

                return $this->device;
            }

            if (!empty($_SERVER['X-Device'])) {
                $this->device = Type::tryFrom($_SERVER['X-Device']) ?? Type::Desktop;
            } else {
                $this->device = $this->drivers->get($this->app->config->device->driver)->get();
            }

            $this->app->session->set('device', $this->device->value);

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

            $this->is_desktop = $this->device == Type::Desktop;

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

            $this->is_mobile = $this->device == Type::Tablet || $this->device == Type::Smartphone;

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

            $this->is_tablet = $this->device == Type::Tablet;

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

            $this->is_smartphone = $this->device == Type::Smartphone;

            return $this->is_smartphone;
        }
    }
}
