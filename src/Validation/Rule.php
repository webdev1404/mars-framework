<?php
/**
* The Base Validator Class
* @package Mars
*/

namespace Mars\Validation;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Base Validator Class
 */
abstract class Rule
{
    use Kernel;

    /**
     * @var string $error The generated error, if any
     */
    public string $error = '';

    /**
     * @var array $error_replacements An array of replacements for the error message
     */
    public array $error_replacements = [];

    /**
     * Validates a value
     * @param string $value The value to validate
     * @param mixed $params Params to be passed to the validator, if any
     * @return bool True if the validation passed
     */
    public function validate(string $value, ...$params) : bool
    {
        return $this->isValid($value, ...$params);
    }

    /**
     * Returns the error message for a field
     * @param string $field The field name
     * @param string $error_name The error name
     * @return string The error message
     */
    public function getError(string $field, string $error_name) : string
    {
        $field_key = ($error_name) ? $error_name : $field;
        $field_val = $this->app->lang->get($field_key);

        $replacements = ['{FIELD}' => $field_val] + $this->error_replacements;

        return $this->app->lang->get($this->error, $replacements);
    }
}
