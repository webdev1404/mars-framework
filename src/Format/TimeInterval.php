<?php
/**
* The Time Interval Format Class
* @package Mars
*/

namespace Mars\Format;

use Mars\App;

/**
 * The Time Interval Format Class
 */
class TimeInterval
{
    /**
     * @see \Mars\Format::timeInterval()
     */
    public function format(int $seconds, string $unit_separator = ' ', string $part_separator = ', ') : string
    {
        if (!$seconds) {
            return '0 ' . App::__('time.seconds');
        }

        $interval = ['seconds' => 0, 'minutes' => 0, 'hours' => 0, 'days' => 0, 'weeks' => 0];

        if ($seconds < 60) {
            $interval['seconds'] = $seconds;
        } else {
            ///compute the minutes
            $interval['minutes'] = floor($seconds / 60);
            $interval['seconds'] = $seconds % 60;
            if ($interval['minutes'] >= 60) {
                ///compute the hours
                $interval['hours'] = floor($interval['minutes'] / 60);
                $interval['minutes'] = $interval['minutes'] % 60;
                if ($interval['hours'] >= 24) {
                    ///compute the days
                    $interval['days'] = floor($interval['hours'] / 24);
                    $interval['hours'] = $interval['hours'] % 24;
                    if ($interval['days'] >= 7) {
                        ///compute the weeks
                        $interval['weeks'] = floor($interval['days'] / 7);
                        $interval['days'] = $interval['days'] % 7;
                    }
                }
            }
        }

        $result = [];

        if ($interval['weeks']) {
            if ($interval['weeks'] == 1) {
                $result[] = $interval['weeks'] . $unit_separator . App::__('time.week');
            } else {
                $result[] = $interval['weeks'] . $unit_separator . App::__('time.weeks');
            }
        }
        if ($interval['days']) {
            if ($interval['days'] == 1) {
                $result[] = $interval['days'] . $unit_separator . App::__('time.day');
            } else {
                $result[] = $interval['days'] . $unit_separator . App::__('time.days');
            }
        }
        if ($interval['hours']) {
            if ($interval['hours'] == 1) {
                $result[] = $interval['hours'] . $unit_separator . App::__('time.hour');
            } else {
                $result[] = $interval['hours'] . $unit_separator . App::__('time.hours');
            }
        }
        if ($interval['minutes']) {
            if ($interval['minutes'] == 1) {
                $result[] = $interval['minutes'] . $unit_separator . App::__('time.minute');
            } else {
                $result[] = $interval['minutes'] . $unit_separator . App::__('time.minutes');
            }
        }
        if ($interval['seconds']) {
            if ($interval['seconds'] == 1) {
                $result[] = $interval['seconds'] . $unit_separator . App::__('time.second');
            } else {
                $result[] = $interval['seconds'] . $unit_separator . App::__('time.seconds');
            }
        }

        return implode($part_separator, $result);
    }
}
