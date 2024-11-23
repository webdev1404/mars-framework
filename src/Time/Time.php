<?php
/**
* The Time Class
* @package Mars
*/

namespace Mars\Time;

/**
 * The Time Class
 * Time related functions
 */
class Time extends Base
{
    /**
     * {@inheritDoc}
     */
    protected string $format = 'H:i:s';

    /**
     * Returns the number of minutes and seconds from $seconds. Eg: for 90 seconds returns 1 min and 30 sec.
     * @param int $seconds The number of seconds
     * @return array Returns an array with the number of minutes & seconds
     */
    public function getMinutes(int $seconds) : array
    {
        $time = ['minutes' => 0, 'seconds' => 0];
        if (!$seconds) {
            return $time;
        }

        $time['minutes'] = floor($seconds / 60);
        $time['seconds'] = $seconds % 60;

        return $time;
    }
}
