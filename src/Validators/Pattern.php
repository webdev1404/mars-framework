<?php
/**
* The Pattern Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Pattern Validator Class
 */
class Pattern extends Validator
{
    /**
     * {@inheritdoc}
     */
    protected string $error = '';

    /**
     * Validates that $value matches a pattern
     * @param string $value The value
     * @param int $max The maximum value
     * @return bool
     */
    public function isValid(string $value, ?string $pattern = null, ?string $error = null) : bool
    {
        if ($pattern == null) {
            throw new \Exception("The Validator Pattern rule must have the pattern specified. Eg: pattern:/[a-Z0-9]*/");
        }

        $this->error = $error ?? 'validate_pattern_error';

        if (!$value) {
            return false;
        }

        return preg_match($pattern, $value, $m);
    }
}
