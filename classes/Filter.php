<?php
/**
* The Filter Class
* @package Mars
*/

namespace Mars;

/**
 * The Filter Class
 * Filters values
 */
class Filter
{
    use AppTrait;

    /**
     * @var Handlers $filters The filters object
     */
    public readonly Handlers $filters;

    /**
     * @var array $supported_filters The list of supported filters
     */
    protected array $supported_filters = [
        'string' => ['string'],
        'int' => ['int'],
        'float' => ['float'],
        'abs' => ['abs'],
        'absint' => ['absint'],
        'absfloat' => ['absfloat'],
        'id' => ['id'],
        'trim' => ['trim'],
        'tags' => ['tags'],
        'html' => '\Mars\Filters\Html',
        'alpha' => '\Mars\Filters\Alpha',
        'alnum' => '\Mars\Filters\Alnum',
        'filename' => '\Mars\Filters\Filename',
        'filepath' => '\Mars\Filters\Filepath',
        'url' => '\Mars\Filters\Url',
        'email' => '\Mars\Filters\Email',
        'slug' => '\Mars\Filters\Slug',
        'interval' => '\Mars\Filters\Interval',
        'exists' => '\Mars\Filters\Exists',
    ];
    /**
     * Builds the text object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->filters = new Handlers($this->supported_filters, $this->app);
    }

    /**
     * Filters a value
     * @param mixed $value The value to filter
     * @param string $filter The filter to apply
     * @return mixed The filtered value
     */
    public function value($value, string $filter)
    {
        if (method_exists($this, $filter)) {
            return $this->$filter($value);
        }

        return $this->filters->map($value, function ($value) use ($filter) {
            return $this->filters->get($filter)->filter($value);
        });
    }

    /**
     * Filters a string value
     * @param $value The value to filter
     * @return string|array The filtered value
     */
    public function string($value) : string|array
    {
        return $this->filters->map($value, function ($value) {
            return (string)$value;
        });
    }

    /**
     * Filters an int value
     * @param $value The value to filter
     * @return int|array The filtered value
     */
    public function int($value) : int|array
    {
        return $this->filters->map($value, function ($value) {
            return (int)$value;
        });
    }

    /**
     * Filters a float value
     * @param $value The value to filter
     * @return float|array The filtered value
     */
    public function float($value) : float|array
    {
        return $this->filters->map($value, function ($value) {
            return (float)$value;
        });
    }

    /**
     * Returns an absolue value
     * @param $value The value to filter
     * @return int|float|array The filtered value
     */
    public function abs($value) : int|float|array
    {
        return $this->filters->map($value, function ($value) {
            return abs($value);
        });
    }

    /**
     * Returns an absolue value from an int
     * @param $value The value to filter
     * @return int|array The filtered value
     */
    public function absint($value) : int|array
    {
        return $this->filters->map($value, function ($value) {
            return abs((int)$value);
        });
    }

    /**
     * Returns an absolue value from a float
     * @param $value The value to filter
     * @return int|array The filtered value
     */
    public function absfloat($value) : int|array
    {
        return $this->filters->map($value, function ($value) {
            return abs((float)$value);
        });
    }

    /**
     * Trims a value
     * @param string|array $value The value
     * @return string|array The filtered value
     */
    public function trim($value) : string|array
    {
        return $this->filters->map($value, function ($value) {
            return trim($value);
        });
    }

    /**
     * Strips the tags from $value
     * @param string|array $value The value
     * @param array|string|null $allowed_tags The tags which should not be removed, if any
     * @return string|array The filtered value
     */
    public function tags($value, array|string|null $allowed_tags = null) : string|array
    {
        return $this->filters->map($value, function ($value) use ($allowed_tags) {
            return strip_tags($value, $allowed_tags);
        });
    }

    /**
     * Filters html using HtmlPurifier
     * @param string $html The $text to filter
     * @param string $allowed_elements String containing the allowed html elements. If null, it will be read from config->html_allowed_elements
     * @param string $allowed_attributes The allowed attributes. If null, it will be read from config->html_allowed_attributes
     * @param string $encoding The encoding of the text
     * @return string The filtered html
     */
    public function html(string $html, string $allowed_elements = null, string $allowed_attributes = null, string $encoding = 'UTF-8') : string
    {
        return $this->filters->map($html, function ($html) use ($allowed_elements, $allowed_attributes, $encoding) {
            return $this->filters->get('html')->filter($html, $allowed_elements, $allowed_attributes, $encoding);
        });
    }

    /**
     * Filters an id value
     * @param int|array $value The value
     * @return int|array The filtered ID value
     */
    public function id(int|array $value) : int|array
    {
        return $this->filters->map($value, function ($value) {
            return abs((int)$value);
        });
    }

    /**
     * Alias for id()
     * @param array $value The value
     * @return array The filtered ID value
     */
    public function ids(array $value) : array
    {
        return $this->id($value);
    }

    /**
     * Filters all non alphabetic chars.
     * @param string|array $value The value
     * @param bool $space If true, will allow spaces
     * @return string|array The filtered value
     */
    public function alpha(string|array $value, bool $space = false) : string|array
    {
        return $this->filters->map($value, function ($value) use ($space) {
            return $this->filters->get('alpha')->filter($value, $space);
        });
    }

    /**
     * Filters all non-alphanumeric chars from $value
     * @param string|array $value The value
     * @param bool $space If true, will allow spaces
     * @return string|array The filtered value
     */
    public function alnum(string|array $value, bool $space = false) : string|array
    {
        return $this->filters->map($value, function ($value) use ($space) {
            return $this->filters->get('alnum')->filter($value, $space);
        });
    }

    /**
     * Filters a filename
     * @param string|array $value The filename to filter
     * @return string|array The filtered filename
     */
    public function filename(string|array $value) : string|array
    {
        return $this->filters->map($value, function ($value) {
            return $this->filters->get('filename')->filter($value);
        });
    }

    /**
     * Filters a filepath
     * !!Only the filename if filtered, the rest of the filepath is left untouched
     * @param string|array $value The filepath to filter
     * @return string|array The filtered filepath
     */
    public function filepath(string|array $value) : string|array
    {
        return $this->filters->map($value, function ($value) {
            return $this->filters->get('filepath')->filter($value);
        });
    }

    /**
     * Filters an url
     * @param string|array $url The url to filter
     * @return string|array The filtered url
     */
    public function url(string|array $url) : string|array
    {
        return $this->filters->map($url, function ($url) {
            return $this->filters->get('url')->filter($url);
        });
    }

    /**
     * Filters an email address
     * @param string|array $email The email to filter (string|array)
     * @return string|array The filtered email
     */
    public function email(string|array $email) : string|array
    {
        return $this->filters->map($email, function ($email) {
            return $this->filters->get('email')->filter($email);
        });
    }

    /**
     * Filters a url slug value
     * @param string|array $value The value to filter
     * @param bool $allow_slash If true will allow slashes in the returned value
     * @return string|array The filtered slug
     */
    public function slug(string|array $value, bool $allow_slash = false) : string|array
    {
        return $this->filters->map($value, function ($value) use ($allow_slash) {
            return $this->filters->get('slug')->filter($value, $allow_slash);
        });
    }

    /**
     * Checks that $value is in the $min - $max interval. If it is, it returns $value. If not returns $default_value
     * @param int|float $value The value
     * @param int|float $min The min. value
     * @param int|float $max The max. value
     * @param int|float $default_value The value to return if $value is not in the $min - $max interval
     * @return int|float The value
     */
    public function interval(int|float $value, int|float $min, int|float $max, int|float $default_value) : int|float
    {
        return $this->filters->map($value, function ($value) use ($min, $max, $default_value) {
            return $this->filters->get('interval')->filter($value, $min, $max, $default_value);
        });
    }

    /**
     * Removes from $value the $remove_value element
     * @param array $values Array with the values
     * @param string|array $remove The value(s) to remove
     * @return array Array with the filtered values
     */
    public function remove(array $values, string|array $remove) : array
    {
        return array_diff($values, App::array($remove));
    }

    /**
     * Removes from $value the elements which aren't found in $allowed
     * @param string|array $value The value(s)
     * @param string|array $allowed Array with the allowed elements
     * @param mixed $not_allowed_value The value returned if $value isn't included in $allowed
     * @return mixed Array with the filtered values
     */
    public function allowed(string|array $value, string|array $allowed, mixed $not_allowed_value = null) : mixed
    {
        $allowed = (array)$allowed;

        if (is_array($value)) {
            return array_intersect($value, $allowed);
        } else {
            if (in_array($value, $allowed)) {
                return $value;
            } else {
                return $not_allowed_value;
            }
        }
    }
}
