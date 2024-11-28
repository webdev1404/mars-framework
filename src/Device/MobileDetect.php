<?php
/**
* The Detect Device Class
* @package Mars
*/

namespace Mars\Device;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Detect Device Class
 * Detects the device a user is using from the useragent
 */
class MobileDetect implements DriverInterface
{
    use InstanceTrait;

    /**
     * @see \Mars\Device\DriverInterface::get()
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
