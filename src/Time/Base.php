<?php
/**
 * The Time Base Class
* @package Mars
*/

namespace Mars\Time;

use Mars\App;
use DateTimeZone;
use DateTime;
use DateInterval;

/**
 * The Time Base Class
 * DateTime related functions
 */
abstract class Base
{
    use \Mars\AppTrait;

    /**
     * @var string $timezone_id The
     */
    public string $timezone_id = 'UTC';

    /**
     * @var \DateTimeZone $timezone The timezone applied to the datetime computations
     */
    public static ?DateTimeZone $timezone = null;

    /**
     * @var string $format
     *             The format string used for time representation.
     */
    protected string $format = '';

    /**
     * @var string $default_value The default value
     */
    protected string $default_value = '';

    /**
     * Builds the time object
     * Sets the default timezone to UTC
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if (!static::$timezone) {
            date_default_timezone_set('UTC');

            $this->setTimezone($this->timezone_id);
        }
    }

    /**
     * Sets the timezone for the DateTime object.
     * @param string $timezone_id The identifier of the timezone to set.
     * @return static
     */
    public function setTimezone(string $timezone_id) : static
    {
        static::$timezone = new DateTimeZone($timezone_id);

        return $this;
    }

    /**
     * Resets the timezone to the default setting.
     * @return static
     */
    public function resetTimezone() : static
    {
        return $this->setTimezone($this->timezone_id);
    }

    /**
     * Returns a DateTime object from a datetime
     * @param int|string|DateTime $datetime The datetime
     * @param bool $is_utc If true, will return the date in the UTC timezone
     * @return DateTime
     */
    public function getDateTime(int|string|DateTime $datetime = 0, bool $is_utc = true) : DateTime
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

        if (!$is_utc) {
            $datetime->setTimezone(static::$timezone);
        }

        return $datetime;
    }

    /**
     * Returns a formatted datetime
     * @param int|string|\DateTime $datetime The datetime
     * @return string The formatted datetime
     */
    public function get(int|string|DateTime $datetime) : string
    {
        if (!$datetime) {
            return $this->default_value;
        }

        return $this->getDateTime($datetime)->format($this->format);
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
