<?php
/**
* The Url Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Url Validator Class
 */
class Url extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'validate.url';

    /**
     * Checks if $value is a valid url
     * @param string $value The value to validate
     * @return bool Returns true if the url is valid
     */
    public function isValid(string $value) : bool
    {
        $scheme = parse_url($value, PHP_URL_SCHEME);
        $blocked_schemes = ['ssh', 'ftp', 'mailto'];

        if ($scheme && in_array(strtolower($scheme), $blocked_schemes)) {
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        return $this->app->plugins->filter('validate.url', true, $value, $this);
    }
}
