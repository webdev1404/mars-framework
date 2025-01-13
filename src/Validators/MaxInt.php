<?php
/**
* The MaxInt Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The MaxInt Validator Class
 * Validates the max value of an integer
 */
class MaxInt extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_maxint_error';

    /**
     * Validates that a value is lower than $max
     * @param string $value The value
     * @param int $max The maximum value
     * @return bool
     */
    public function isValid(string $value, ?int $max = null) : bool
    {
        if ($max === null) {
            throw new \Exception("The max_int validator rule must have the max number specified. Eg: max_number:5");
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
