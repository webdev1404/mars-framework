<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Min Validator Class
 * Validates the min value of a number
 */
class Min extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error = 'validate_float_min_error';

    /**
     * Validates the min value of a number
     * @param string $value The value
     * @param int $min The minimum value
     * @return bool
     */
    public function isValid(string $value, ?int $min = null) : bool
    {
        if ($min === null) {
            throw new \Exception("The min validator rule must have the minimum number specified. Eg: min:5");
        }

        $this->error_replacements = ['{MIN}' => $min];

        if (!is_numeric($value)) {
            return false;
        }

        $value = (float)$value;
        if ($value >= $min) {
            return true;
        }

        return false;
    }
}
