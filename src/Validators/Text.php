<?php
/**
* The Text Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Text Validator Class
 * Validates the number of chars of a string
 */
class Text extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error_string = 'validate_text_error';

    /**
     * Validates the number of chars of a string
     * @param string $value The value
     * @param int $min The minimum no. of chars
     * @param int $max The maximum no. of chars
     * @return bool
     */
    public function isValid(string $value, ?int $min = null, ?int $max = null) : bool
    {    
        $length = mb_strlen($value);

        $min = $min ?? 0;
        if ($max) {
            if ($length >= $min && $length <= $max) {
                return true;
            }
        } else {
            if ($length >= $min) {
                return true;
            }
        }
        
        return false;
    }
}
