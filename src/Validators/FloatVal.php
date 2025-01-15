<?php
/**
* The Integer Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Integer Validator Class
 * Validates that value is a float
 */
class FloatVal extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_float_error';

    /**
     * Validates that value is a integer
     * @param string $value The value
     * @param float $min The minimum value, if any
     * @param float $max The maximum value, if any
     * @return bool
     */
    public function isValid(string $value, ?float $min = null, ?float $max = null) : bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        if (!$min && !$max) {
            return true;
        }

        $value = (float)$value;
        if ($min && $max) {
            if ($value >= $min && $value <= $max) {
                return true;
            }
        } elseif ($min) {
            if ($value >= $min) {
                return true;
            }
        } elseif ($max) {
            if ($value <= $max) {
                return true;
            }
        }

        return false;
    }
}
