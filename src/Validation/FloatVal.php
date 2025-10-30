<?php
/**
* The Integer Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Integer Validator Class
 * Validates that value is a float
 */
class FloatVal extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = '';

    public array $errors = [
        'simple' => 'error.validate_float',
        'min' => 'error.validate_float_min',
        'max' => 'error.validate_float_max',
        'min_max' => 'error.validate_float_min_max',
    ];

    /**
     * Validates that value is a integer
     * @param string $value The value
     * @param float $min The minimum value, if any
     * @param float $max The maximum value, if any
     * @return bool
     */
    public function isValid(string $value, ?float $min = 0, ?float $max = null) : bool
    {
        return $this->isValidValue($value, $min, $max, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Validates the value
     * @return bool Returns true if the value is valid
     */
    protected function isValidValue(mixed $value, ?float $min, ?float $max, int $type) : bool
    {
        $this->error = $this->errors['simple'];

        if (!trim($value) && !$min && $max === null) {
            // If no value and no min/max, consider it valid
            return true;
        }

        if (!filter_var($value, $type)) {
            return false;
        }

        return $this->isValidMinMax((float)$value, $min, $max);
    }

    /**
     * Validates the minimum and maximum values
     * @param float $value The value to validate
     * @param float $min The minimum value, if any
     * @param float $max The maximum value, if any
     * @return bool Returns true if the value is within the min and max range
     */
    protected function isValidMinMax($value, ?float $min = null, ?float $max = null) : bool
    {
        if ($min === null && $max === null) {
            return true;
        }

        $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];

        $value = (float)$value;
        if ($min && $max) {
            $this->error = $this->errors['min_max'];

            if ($value >= $min && $value <= $max) {
                return true;
            }
        } elseif ($min !== null) {
            $this->error = $this->errors['min'];

            if ($value >= $min) {
                return true;
            }
        } elseif ($max !== null) {
            $this->error = $this->errors['max'];

            if ($value <= $max) {
                return true;
            }
        }

        return false;
    }
}
