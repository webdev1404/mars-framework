<?php
/**
* The Max Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Max Validator Class
 * Validates the max value of a number
 */
class Max extends Rule
{
    /**
     * {@inheritDoc}
     */
    public string $error = 'validate.float.max';

    /**
     * Validates the max value of a number
     * @param string $value The value
     * @param ?float $max The maximum value
     * @return bool
     */
    public function isValid(string $value, ?float $max = null) : bool
    {
        if ($max === null) {
            throw new \Exception("The 'max' validation rule must have the maximum number specified. Eg: max:5");
        }

        $this->error_replacements = ['{MAX}' => $max];

        if (!is_numeric($value)) {
            return false;
        }

        $value = (float)$value;
        if ($value <= $max) {
            return true;
        }

        return false;
    }
}
