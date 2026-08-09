<?php
/**
* The Float Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Float Validator Class
 * Validates that value is a float
 */
class FloatVal extends Rule
{
    /**
     * {@inheritDoc}
     */
    public string $error = '';

    public array $errors = [
        'simple' => 'validate.float',
        'min' => 'validate.float.min',
        'max' => 'validate.float.max',
        'min_max' => 'validate.float.min_max',
    ];

    /**
     * Validates that value is a float
     * @param string $value The value
     * @param ?float $min The minimum value, if any
     * @param ?float $max The maximum value, if any
     * @return bool
     */
    public function isValid(string $value, ?float $min = 0, ?float $max = null) : bool
    {
        return $this->isValidValue($value, $min, $max, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Validates the value
     * @param mixed $value The value
     * @param ?float $min The minimum value, if any
     * @param ?float $max The maximum value, if any
     * @param int $type The filter type
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
     * @param ?float $min The minimum value, if any
     * @param ?float $max The maximum value, if any
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
