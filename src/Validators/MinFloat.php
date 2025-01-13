<?php
/**
* The MinFloat Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The MinFloat Validator Class
 * Validates the min value of a float
 */
class MinFloat extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_minfloat_error';

    /**
     * Validates that a value is greater than $min
     * @param string $value The value
     * @param int|float $min The minimum value
     * @return bool
     */
    public function isValid(string $value, int|float|null $min = null) : bool
    {
        if ($min === null) {
            throw new \Exception("The min_float validator rule must have the minimum number specified. Eg: min_float:5");
        }

        if (!is_numeric($value)) {
            return false;
        }

        if ($value >= $min) {
            return true;
        }

        return false;
    }
}
