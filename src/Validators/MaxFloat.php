<?php
/**
* The MaxFloat Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The MaxFloat Validator Class
 * Validates the max value of a float
 */
class MaxFloat extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_maxfloat_error';

    /**
     * Validates that a value is lower than $max
     * @param string $value The value
     * @param int|float $max The maximum value
     * @return bool
     */
    public function isValid(string $value, int|float|null $max = null) : bool
    {
        if ($max === null) {
            throw new \Exception("The max_float validator rule must have the max number specified. Eg: max_number:5");
        }

        if (!is_numeric($value)) {
            return false;
        }

        if ($value <= $max) {
            return true;
        }

        return false;
    }
}
