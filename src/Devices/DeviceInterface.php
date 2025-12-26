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
     * Returns the device's type
     * @param ?string $useragent The useragent. If null, the user's useragent is used
     * @return Type
     */
    public function get(?string $useragent = null) : Type;
}
