<?php
/**
* The Timestamp Class
* @package Mars
*/

namespace Mars\Time;

use DateTime;

/**
 * The Timestamp Class
 * Timestamp related functions
 */
class Timestamp extends Base
{
    /**
     * Returns a timestamp from a datetime
     * @param int|string|\DateTime $datetime The datetime
     * @return string|null The timestamp
     */
    public function get(int|string|DateTime $datetime, ?string $format = null) : string|null
    {
        if (!$datetime) {
            return 0;
        }

        return $this->getDateTime($datetime)->getTimestamp();
    }
}
