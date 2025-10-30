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
     * @param int $max The maximum value
     * @return bool
     */
    public function isValid(string $value, ?string $pattern = null, ?string $error = null) : bool
    {
        if ($pattern == null) {
            throw new \Exception("The 'pattern' validation rule must have the pattern specified. Eg: pattern:/[a-Z0-9]*/");
        }

        $this->error = $error ?? 'error.validate_pattern';

        if (!$value) {
            return false;
        }

        return preg_match($pattern, $value, $m);
    }
}
