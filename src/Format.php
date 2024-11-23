<?php
/**
* The Format Class
* @package Mars
*/

namespace Mars;

use DateTime;
use Mars\App\InstanceTrait;

/**
 * The Format Class
 * Converts values using a certain format
 */
class Format
{
    use InstanceTrait;

    /**
     * @var string $datetime_format The datetime format
     */
    protected string $datetime_format = 'D M d, Y h:i:s';

    /**
     * @var string $date_format The date format
     */
    protected string $date_format = 'D M d, Y';

    /**
     * @var string $time_format The time format
     */
    protected string $time_format = 'h:i:s';

    /**
     * @var Handlers $formats The formats object
     */
    public readonly Handlers $formats;

    /**
     * @var array $supported_formats The list of supported formats
     */
    protected array $supported_formats = [
        'lower' => ['lower'],
        'upper' => ['upper'],
        'round' => ['round'],
        'number' => ['number'],
        'datetime' => ['datetime'],
        'date' => ['date'],
        'time' => ['time'],
        'percentage' => '\Mars\Formats\Percentage',
        'filesize' => '\Mars\Formats\Filesize',
        'time_interval' => '\Mars\Formats\TimeInterval',
        'js_array' => '\Mars\Formats\JsArray',
        'js_object' => '\Mars\Formats\JsObject',
    ];

    /**
     * Builds the text object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->formats = new Handlers($this->supported_formats, $this->app);
    }

    /**
     * Converts a value to lowercase
     * @param string|array $value The value
     * @return string|array The formatted value
     */
    public function lower(string|array $value) : string|array
    {
        return $this->formats->map($value, function ($value) {
            return strtolower($value);
        });
    }

    /**
     * Converts a value to lowercase
     * @param string|array $value The value
     * @return string|array The formatted value
     */
    public function upper(string|array $value) : string|array
    {
        return $this->formats->map($value, function ($value) {
            return strtoupper($value);
        });
    }

    /**
     * Rounds a float
     * @param float|array $value The value to round
     * @param int $decimals The number of decimals to round to
     * @return float The rounded value
     */
    public function round(float|array $value, int $decimals = 2) : float|array
    {
        return $this->formats->map($value, function ($value) use ($decimals) {
            return round($value, $decimals);
        });
    }

    /**
     * Format a number with grouped thousands
     * @param float|array $number The number being formatted
     * @param int $decimals The number of decimal points
     * @param string $decimal_separator The separator for the decimal point
     * @param string $thousands_separator The thousands separator
     * @return string The formatted number
     */
    public function number(float|array $number, int $decimals = 2, string $decimal_separator = '.', string $thousands_separator = ',') : string|array
    {
        return $this->formats->map($number, function ($number) use ($decimals, $decimal_separator, $thousands_separator) {
            return number_format($number, $decimals, $decimal_separator, $thousands_separator);
        });
    }

    /**
     * Returns the percentage of $number from $total
     * @param float|array $number The number
     * @param float $total The total
     * @param int $decimals The number of decimal points
     * @return string The percentage
     */
    public function percentage(float|array $number, float $total, int $decimals = 4) : float|array
    {
        return $this->formats->map($number, function ($number) use ($total, $decimals) {
            return $this->formats->get('percentage')->format($number, $total, $decimals);
        });
    }

    /**
     * Formats a filesize. It returns the result in gb, mb or kb depending on the $kb parameter
     * @param int|float|array $bytes The filesize - in bytes - to be converted.
     * @param int $digits The number of digits to return to the result if it's MBs.
     * @return string The formatted filesize
     */
    public function filesize(int|float|array $bytes, int $digits = 2) : string|array
    {
        return $this->formats->map($bytes, function ($bytes) use ($digits) {
            return $this->formats->get('filesize')->format($bytes, $digits);
        });
    }

    /**
     * Formats a datetime
     * @param int|string|DateTime $datetime The datetime
     * @return string The formatted value
     */
    public function datetime(int|string|DateTime|array $datetime = 0) : string|array
    {
        return $this->formats->map($datetime, function ($datetime) {
            return $this->app->datetime->get($datetime);
        });
    }

    /**
     * Formats a date
     * @param int|string|DateTime $date The date
     * @return string The formatted value
     */
    public function date(int|string|DateTime|array $date = 0) : string|array
    {
        return $this->formats->map($date, function ($date) {
            return $this->app->date->get($date);
        });
    }

    /**
     * Formats time
     * @param int|string|DateTime $time The time
     * @return string The formatted value
     */
    public function time(int|string|DateTime|array $time = 0) : string|array
    {
        return $this->formats->map($time, function ($time) {
            return $this->app->time->get($time);
        });
    }

    /**
     * Formats a time interval. It returns the number of weeks,days,hours,minutes,seconds it contains. Eg: 90 = 1 minute,30 seconds
     * @param int $seconds The number of seconds
     * @param string $separator1 The separator between the numeric value and the word. Eg: separator = : the result will be 2:weeks etc..
     * @param string $separator2 The separator from the end of a value. Eg:separator = , result= 2weeks,3days..
     * @return string The formatted value
     */
    public function timeInterval(int|array $seconds, string $separator1 = ' ', string $separator2 = ', ') : string|array
    {
        return $this->formats->map($seconds, function ($seconds) use ($separator1, $separator2) {
            return $this->formats->get('time_interval')->format($seconds, $separator1, $separator2);
        });
    }

    /**
     * Returns a javascript array from $data
     * @param array $data The data to convert to a javascript array
     * @param bool $quote If true will put quotes around the array's elements
     * @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
     * @return string The javascript array
     */
    public function jsArray(array $data, bool $quote = true, array $dont_quote_array = []) : string
    {
        return $this->formats->get('js_array')->format($data, $quote, $dont_quote_array);
    }

    /**
     * Returns a javascript object from $data
     * @param array|object $data The data to convert to a javascript object
     * @param bool $quote If true will put quotes around the array's elements
     * @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
     * @return string The javascript object
     */
    public function jsObject(array|object$data, bool $quote = true, array $dont_quote_array = [])
    {
        return $this->formats->get('js_object')->format($data, $quote, $dont_quote_array);
    }
}
