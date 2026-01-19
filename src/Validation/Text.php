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

    /**
     * {@inheritdoc}
     */
    public array $errors = [
        'min' => 'validate.text.min',
        'max' => 'validate.text.max',
    ];

    /**
     * Validates that a value is not empty and length is within min and max limits
     * @param string $value The value
     * @param int|null $min The minimum length
     * @param int|null $max The maximum length
     * @return bool
     */
    public function isValid(string $value, ?int $min = null, ?int $max = null) : bool
    {
        $length = mb_strlen(trim($value));

        if (!$min && !$max) {
            return $length > 0;
        }

        $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];

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
