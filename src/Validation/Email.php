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
    protected string $error = 'error.validate_email';

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

        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
