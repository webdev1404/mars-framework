<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Device\DeviceInterface;
use Mars\Device\Type;

/**
 * The Device Class
 * Encapsulates the user's device
 */
class Device
{
    use Kernel;

    /**
     * @var array $drivers_list The supported drivers list
     */
    public protected(set) array $drivers_list = [
        'mobile_detect' => \Mars\Device\MobileDetect::class
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->drivers_list, DeviceInterface::class, 'device', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var Type $device The device type. Eg: desktop/tablet/smartphone
     */
    public protected(set) Type $type {
        get {
            if (isset($this->type)) {
                return $this->type;
            }

            $this->type = Type::Desktop;
            if ($this->app->is_cli) {
                return $this->type;
            }

            if ($this->app->config->development->device) {
                $this->type = Type::from($this->app->config->development->device);

                return $this->type;
            }

            $session_device = $this->app->session->get('device');
            if ($session_device) {
                $this->type = Type::tryFrom($session_device) ?? Type::Desktop;

                return $this->type;
            }

            if (!empty($_SERVER['X-Device'])) {
                $this->type = Type::tryFrom($_SERVER['X-Device']) ?? Type::Desktop;
            } else {
                $this->type = $this->drivers->get($this->app->config->device->driver)->get();
            }

            $this->app->session->set('device', $this->type->value);

            return $this->type;
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

            $this->is_desktop = $this->type == Type::Desktop;

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

            $this->is_mobile = $this->type == Type::Tablet || $this->type == Type::Smartphone;

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

            $this->is_tablet = $this->type == Type::Tablet;

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

            $this->is_smartphone = $this->type == Type::Smartphone;

            return $this->is_smartphone;
        }
    }
}
