<?php
/**
* The Valid Device Types
* @package Mars
*/

namespace Mars\Devices;

/**
 * The Valid Device Types
 */
enum Type : string
{
    case Desktop = 'desktop';
    case Tablet = 'tablet';
    case Smartphone = 'smartphone';
}
