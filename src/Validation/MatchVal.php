<?php
/**
* The Match Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Match Validator Class
 */
class MatchVal extends Rule
{
    /**
     * {@inheritDoc}
     */
    public string $error = 'validate.match';

    /**
     * Validates a match
     * @param string $value The value to match
     * @param ?string $match The value to match against
     * @return bool
     */
    public function isValid(string $value, ?string $match = null) : bool
    {
        if ($match === null) {
            throw new \InvalidArgumentException('The match parameter is required for the Match validation rule.');
        }

        $value = trim($value);
        $match = trim($match);

        return $value === $match;
    }
}
