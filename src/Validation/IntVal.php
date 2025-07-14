<?php
/**
* The Integer Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Integer Validator Class
 * Validates that value is a integer
 */
class IntVal extends FloatVal
{
    protected array $errors = [
        'simple' => 'error.validate_int',
        'min' => 'error.validate_int_min',
        'max' => 'error.validate_int_max',
        'min_max' => 'error.validate_int_min_max',
    ];

    /**
     * Validates that value is a integer
     * @param string $value The value
     * @param int $min The minimum value, if any
     * @param int $max The maximum value, if any
     * @return bool
     */
    public function isValid(string $value, ?float $min = 0, ?float $max = null) : bool
    {
        return $this->isValidValue($value, $min, $max, FILTER_VALIDATE_INT);
    }
}
