<?php
/**
* The Detect Device Class
* @package Mars
*/

namespace Mars\Devices;

use Mars\App\Kernel;

/**
 * The Detect Device Class
 * Detects the device a user is using from the useragent
 */
class MobileDetect implements DeviceInterface
{
    use Kernel;

    /**
     * @see DeviceInterface::get()
     * {@inheritDoc}
     */
    public function get(?string $useragent = null) : Type
    {
        $useragent ??= $this->app->useragent;

        $detector = new \Detection\MobileDetect;
        $detector->setUserAgent($useragent);

        if ($detector->isTablet()) {
            return Type::Tablet;
        } elseif ($detector->isMobile()) {
            return Type::Smartphone;
        }

        return Type::Desktop;
    }
}
