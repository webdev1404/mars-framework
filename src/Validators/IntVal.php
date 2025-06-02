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
class IntVal extends FloatVal
{
    protected array $errors = [
        'simple' => 'validate_int_error',
        'min' => 'validate_int_min_error',
        'max' => 'validate_int_max_error',
        'min_max' => 'validate_int_min_max_error',
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
