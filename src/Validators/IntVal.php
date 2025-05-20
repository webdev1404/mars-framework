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
class IntVal extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_int_error';

    /**
     * Validates that value is a integer
     * @param string $value The value
     * @param int $min The minimum value, if any
     * @param int $max The maximum value, if any
     * @return bool
     */
    public function isValid(string $value, ?int $min = null, ?int $max = null) : bool
    {
        if (!ctype_digit($value)) {
            return false;
        }
        if (!$min && !$max) {
            return true;
        }

        $value = (int)$value;
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
