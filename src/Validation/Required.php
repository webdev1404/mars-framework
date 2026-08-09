<?php
/**
* The Required Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Required Validator Class
 */
class Required extends Rule
{
    /**
     * {@inheritDoc}
     */
    public string $error = '';

    /**
     * {@inheritDoc}
     */
    public array $errors = [
        'required' => 'validate.required',
        'min' => 'validate.required.min',
        'max' => 'validate.required.max',
    ];

    /**
     * Validates that a value is not empty
     * @param string $value The value
     * @param int|null $min The minimum length, if any
     * @param int|null $max The maximum length, if any
     * @return bool
     */
    public function isValid(string $value, ?int $min = null, ?int $max = null) : bool
    {
        $this->error = $this->errors['required'];

        if (!trim($value)) {
            return false;
        }

        if (!$min && !$max) {
            return true;
        }

        $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];

        $length = mb_strlen($value);

        $min = $min ?? 0;
        if ($max) {
            $this->error = $this->errors['max'];

            if ($length >= $min && $length <= $max) {
                return true;
            }
        } else {
            $this->error = $this->errors['min'];

            if ($length >= $min) {
                return true;
            }
        }

        return false;
    }
}
