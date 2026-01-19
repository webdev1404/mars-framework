<?php
/**
* The Pattern Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Pattern Validator Class
 */
class Pattern extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = '';

    /**
     * Validates that $value matches a pattern
     * @param string $value The value
     * @param ?string $pattern The pattern
     * @param ?string $error The error message key to use, if any
     * @return bool
     */
    public function isValid(string $value, ?string $pattern = null, ?string $error = null) : bool
    {
        if ($pattern === null) {
            throw new \Exception("The 'pattern' validation rule must have the pattern specified. Eg: pattern:/[a-Z0-9]*/");
        }

        $this->error = $error ?? 'validate.pattern';

        if (!$value) {
            return false;
        }

        return preg_match($pattern, $value, $m);
    }
}
