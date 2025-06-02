<?php
/**
* The Required Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Required Validator Class
 */
class Required extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error = '';

    protected array $errors = [
        'required' => 'validate_required_error',
        'min' => 'validate_required_min_error',
        'max' => 'validate_required_max_error',
    ];

    /**
     * Validates that a value is not empty
     * @param string $value The value
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
