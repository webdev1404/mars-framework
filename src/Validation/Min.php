<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Min Validator Class
 * Validates the min value of a number
 */
class Min extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'validate.float.min';

    /**
     * Validates the min value of a number
     * @param string $value The value
     * @param int $min The minimum value
     * @return bool
     */
    public function isValid(string $value, ?int $min = null) : bool
    {
        if ($min === null) {
            throw new \Exception("The 'min' validation rule must have the minimum number specified. Eg: min:5");
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
