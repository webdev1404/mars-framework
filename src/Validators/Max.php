<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Max Validator Class
 * Validates the max number of chars of a string
 */
class Max extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_max_error';

    /**
     * Validates the number of chars of a string
     * @param string $value The value
     * @param int $length The minimum length of the string
     * @return bool
     */
    public function isValid(string $value, ?int $length = null) : bool
    {
        if ($length === null) {
            throw new \Exception("The max validator rule must have the max number of chars. specified. Eg: max:5");
        }

        if (mb_strlen($value) <= $length) {
            return true;
        }

        return false;
    }
}
