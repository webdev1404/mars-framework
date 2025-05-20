<?php
/**
* The MinInt Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The MinInt Validator Class
 * Validates the min value of an integer
 */
class MinInt extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_minint_error';

    /**
     * Validates that a value is greater than $min
     * @param string $value The value
     * @param int $min The minimum value
     * @return bool
     */
    public function isValid(string $value, ?int $min = null) : bool
    {
        if ($min === null) {
            throw new \Exception("The min_int validator rule must have the minimum number specified. Eg: min_int:5");
        }

        if (!ctype_digit($value)) {
            return false;
        }

        if ($value >= $min) {
            return true;
        }

        return false;
    }
}
