<?php
/**
* The Captcha Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Captcha\CaptchaInterface;

/**
 * The Captcha Class
 * Class which provides captcha functionality
 */
class Captcha
{
    use Kernel;
    
    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'recaptcha2' => \Mars\Captcha\Recaptcha2::class
    ];

    /**
     * @var bool $enabled Will be set to true, if captcha is enabled
     */
    public bool $enabled {
        get => $this->app->config->captcha_enable;
    }

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, CaptchaInterface::class, 'captcha', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var CaptchaInterface $driver The driver object
     */
    public protected(set) ?CaptchaInterface $driver {
        get {
            if (!$this->enabled) {
                return null;
            }
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->captcha_driver);

            return $this->driver;
        }
    }

    /**
     * Checks the captcha is correct
     * @return bool Returns bool if the captcha is correct
     */
    public function check() : bool
    {
        if (!$this->enabled) {
            return true;
        }

        return $this->driver->check();
    }

    /**
     * Outputs the captcha
     */
    public function output()
    {
        if (!$this->enabled) {
            return;
        }

        echo '<div class="captcha">';
        $this->driver->output();
        echo '</div>';
    }
}
