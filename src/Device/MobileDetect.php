<?php
/**
* The Detect Device Class
* @package Mars
*/

namespace Mars\Device;

use Mars\App\Kernel;

/**
 * The Detect Device Class
 * Detects the device a user is using from the user agent
 */
class MobileDetect implements DeviceInterface
{
    use Kernel;

    /**
     * @see DeviceInterface::get()
     * {@inheritDoc}
     */
    public function get(?string $user_agent = null) : Type
    {
        $user_agent ??= $this->app->user_agent;

        $detector = new \Detection\MobileDetect;
        $detector->setUserAgent($user_agent);

        if ($detector->isTablet()) {
            return Type::Tablet;
        } elseif ($detector->isMobile()) {
            return Type::Smartphone;
        }

        return Type::Desktop;
    }
}
