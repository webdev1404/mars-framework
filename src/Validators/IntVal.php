<?php
/**
* The Integer Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Integer Validator Class
 * Validates that value is a integer
 */
class IntVal extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_int_error';

    /**
     * Validates that value is a integer
     * @param string $value The value
     * @param int $min The minimum no. of chars
     * @param int $max The maximum no. of chars
     * @return bool
     */
    public function isValid(string $value) : bool
    {
        return ctype_digit($value);
    }
}
