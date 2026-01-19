<?php
/**
* The Format Class
* @package Mars
*/

namespace Mars;

use DateTime;
use Mars\App\Kernel;
use Mars\App\Handlers;

/**
 * The Format Class
 * Converts values using a certain format
 */
class Format
{
    use Kernel;
    
    /**
     * @var array $supported_formats The list of supported formats
     */
    public protected(set) array $supported_formats = [
        'percentage' => \Mars\Format\Percentage::class,
        'filesize' => \Mars\Format\Filesize::class,
        'time_interval' => \Mars\Format\TimeInterval::class
    ];

    /**
     * @var Handlers $format The format object
     */
    public protected(set) Handlers $format {
        get {
            if (isset($this->format)) {
                return $this->format;
            }

            $this->format = new Handlers($this->supported_formats, null, $this->app);

            return $this->format;
        }
    }

    /**
     * Converts a value to lowercase
     * @param string|array $value The value
     * @return string|array The formatted value
     */
    public function lower(string|array $value) : string|array
    {
        return $this->app->data->map($value, function ($value) {
            return strtolower($value);
        });
    }

    /**
     * Converts a value to uppercase
     * @param string|array $value The value
     * @return string|array The formatted value
     */
    public function upper(string|array $value) : string|array
    {
        return $this->app->data->map($value, function ($value) {
            return strtoupper($value);
        });
    }

    /**
     * Rounds a float
     * @param float|array $value The value to round
     * @param int $decimals The number of decimals to round to
     * @return float|array The rounded value
     */
    public function round(float|array $value, int $decimals = 2) : float|array
    {
        return $this->app->data->map($value, function ($value) use ($decimals) {
            return round($value, $decimals);
        });
    }

    /**
     * Format a number with grouped thousands
     * @param float|array $number The number being formatted
     * @param int $decimals The number of decimal points
     * @param string $decimal_separator The separator for the decimal point
     * @param string $thousands_separator The thousands separator
     * @return string|array The formatted number
     */
    public function number(float|array $number, int $decimals = 2, string $decimal_separator = '.', string $thousands_separator = ',') : string|array
    {
        return $this->app->data->map($number, function ($number) use ($decimals, $decimal_separator, $thousands_separator) {
            return number_format($number, $decimals, $decimal_separator, $thousands_separator);
        });
    }

    /**
     * Returns the percentage of $number from $total
     * @param float|array $number The number
     * @param float $total The total
     * @param int $decimals The number of decimal points
     * @return float|array The percentage
     */
    public function percentage(float|array $number, float $total, int $decimals = 4) : float|array
    {
        return $this->app->data->map($number, function ($number) use ($total, $decimals) {
            return $this->format->get('percentage')->format($number, $total, $decimals);
        });
    }

    /**
     * Formats a filesize into a human-readable unit (GB, MB, KB) using the given precision
     * @param int|float|array $bytes The filesize - in bytes - to be converted.
     * @param int $digits The number of digits to return to the result if it's MBs.
     * @return string|array The formatted filesize
     */
    public function filesize(int|float|array $bytes, int $digits = 2) : string|array
    {
        return $this->app->data->map($bytes, function ($bytes) use ($digits) {
            return $this->format->get('filesize')->format($bytes, $digits);
        });
    }

    /**
     * Formats a datetime
     * @param int|string|DateTime|array $datetime The datetime
     * @return string|array The formatted value
     */
    public function datetime(int|string|DateTime|array $datetime = 0) : string|array
    {
        return $this->app->data->map($datetime, function ($datetime) {
            return $this->app->datetime->get($datetime);
        });
    }

    /**
     * Formats a date
     * @param int|string|DateTime|array $date The date
     * @return string|array The formatted value
     */
    public function date(int|string|DateTime|array $date = 0) : string|array
    {
        return $this->app->data->map($date, function ($date) {
            return $this->app->date->get($date);
        });
    }

    /**
     * Formats time
     * @param int|string|DateTime|array $time The time
     * @return string|array The formatted value
     */
    public function time(int|string|DateTime|array $time = 0) : string|array
    {
        return $this->app->data->map($time, function ($time) {
            return $this->app->time->get($time);
        });
    }

    /**
     * Formats a time interval. It returns the number of weeks, days, hours, minutes, seconds it contains. Eg: 90 = 1 minute, 30 seconds
     * @param int|array $seconds The number of seconds
     * @param string $unit_separator The separator between the numeric value and the word. Eg: separator = : the result will be 2:weeks etc.
     * @param string $part_separator The separator between parts Eg: separator = , result = 2 weeks, 3 days.
     * @return string|array The formatted value
     */
    public function timeInterval(int|array $seconds, string $unit_separator = ' ', string $part_separator = ', ') : string|array
    {
        return $this->app->data->map($seconds, function ($seconds) use ($unit_separator, $part_separator) {
            return $this->format->get('time_interval')->format($seconds, $unit_separator, $part_separator);
        });
    }
}
