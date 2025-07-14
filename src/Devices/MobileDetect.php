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
     * {@inheritdoc}
     */
    public function get(string $useragent = '') : string
    {
        $useragent = $useragent ? $useragent : $this->app->useragent;
        $handle = new \Detection\MobileDetect;
        $handle->setUserAgent($useragent);

        if ($handle->isTablet()) {
            return 'tablet';
        } elseif ($handle->isMobile()) {
            return 'smartphone';
        }

        return 'desktop';
    }
}
