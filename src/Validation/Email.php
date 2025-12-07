<?php
/**
* The Email Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Email Validator Class
 */
class Email extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'validate.email';

    /**
     * Checks if $value is a valid email address
     * @param string $value The email to validate
     * @return bool Returns true if the email is valid
     */
    public function isValid(string $value) : bool
    {
        if (!$value) {
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $this->app->plugins->filter('validate_email', true, $value, $this);
    }
}
