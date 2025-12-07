<?php
/**
* The Text Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Text Validator Class
 */
class Text extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = '';

    public array $errors = [
        'min' => 'validate.text.min',
        'max' => 'validate.text.max',
    ];

    /**
     * Validates that a value is not empty
     * @param string $value The value
     * @return bool
     */
    public function isValid(string $value, ?int $min = null, ?int $max = null) : bool
    {
        if (!$min && !$max) {
            return true;
        }

        $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];

        $length = mb_strlen($value);

        $min ??= 0;
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
