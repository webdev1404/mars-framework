<?php
/**
 * The Time Base Class
* @package Mars
*/

namespace Mars\Time;

use DateTime;
use DateInterval;
use Mars\App;
use Mars\App\Kernel;

/**
 * The Time Base Class
 * DateTime related functions
 */
abstract class Base
{
    use Kernel;
    
    /**
     * @var string $format The format string used for time representation.
     */
    protected string $format = '';

    /**
     * @var string $default_value The default value
     */
    protected ?string $default_value = null;    

    /**
     * Returns a DateTime object from a datetime
     * @param int|string|DateTime $datetime The datetime
     * @return DateTime
     */
    public function getDateTime(int|string|DateTime $datetime = 0) : DateTime
    {
        if (!$datetime instanceof DateTime) {
            if (!$datetime) {
                $datetime = 'now';
            }

            if (is_numeric($datetime)) {
                $datetime = '@' . $datetime;
            }

            $datetime = new DateTime($datetime);
        }

        $datetime->setTimezone($this->app->timezone->timezone);

        return $datetime;
    }

    /**
     * Returns a formatted datetime
     * @param int|string|\DateTime $datetime The datetime
     * @param string|null $format The format. If null, the default format will be used
     * @return string|null The formatted datetime
     */
    public function get(int|string|DateTime $datetime, ?string $format = null) : string|null
    {
        if (!$datetime) {
            return $this->default_value;
        }

        $format ??= $this->format;

        return $this->getDateTime($datetime)->format($format);
    }

    /**
     * Adds to $datetime a certain number of days/months/weeks/years
     * @param int $units The number of time units to add
     * @param string $type The interval type: days/months/weeks/years
     * @param int|string|DateTime $datetime The datetime. If 0, the current time will be used
     * @return DateTime The new date
     */
    public function add(int $units, string $type, int|string|DateTime $datetime = 0) : string
    {
        $interval = $this->getDateInterval($units, $type);

        return $this->get($this->getDateTime($datetime)->add($interval));
    }

    /**
     * Subtracts from $datetime a certain number of days/months/weeks/years
     * @param int $units The number of time units to  subtract
     * @param string $type The interval type: days/months/weeks/years
     * @param int|string|DateTime $datetime The datetime. If 0, the current time will be used
     * @return DateTime The new date
     */
    public function sub(int $units, string $type, int|string|DateTime $datetime = 0) : string
    {
        $interval = $this->getDateInterval($units, $type);

        return $this->get($this->getDateTime($datetime)->sub($interval));
    }

    /**
     * Creates a DateInterval object
     * @param int $units The number of time units
     * @param string $type The interval type: days/months/weeks/years
     * @return DateInterval
     */
    protected function getDateInterval(int $units, string $type) : DateInterval
    {
        $duration = '';
        $type = strtolower(trim($type));

        switch ($type) {
            case 'day':
            case 'days':
                $duration = "P{$units}D";
                break;
            case 'week':
            case 'weeks':
                $duration = "P{$units}W";
                break;
            case 'month':
            case 'months':
                $duration = "P{$units}M";
                break;
            case 'year':
            case 'years':
                $duration = "P{$units}Y";
                break;
            default:
                $duration = 'P0D';
        }

        return new DateInterval($duration);
    }
}
