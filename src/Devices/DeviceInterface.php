<?php
/**
* The Device Detector Driver Interface
* @package Mars
*/

namespace Mars\Devices;

/**
 * The Device Detector Driver Interface
 */
interface DeviceInterface
{
    /**
     * Returns the device's type: desktop,tablet,smartphone
     * @param string $useragent The useragent. If empty, the user's useragent is used
     * @return string
     */
    public function get(string $useragent = '') : string;
}
