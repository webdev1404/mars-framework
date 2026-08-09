<?php
/**
* The Username Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Username Validator Class
 */
class Username extends Rule
{
    /**
     * {@inheritDoc}
     */
    public string $error = 'validate.username';

    public array $errors = [
        'min_max' => 'validate.username.length',
        'chars' => 'validate.username.chars',
    ];

    protected int $min = 5;
    protected int $max = 100;

    /**
     * Validates a username
     * @param string $username The username
     * @param int|null $min The minimum allowed username length, or null to use the default minimum length.
     * @param int|null $max The maximum allowed username length, or null to use the default maximum length.
     * @return bool
     */
    public function isValid(string $username, ?int $min = null, ?int $max = null) : bool
    {
        $min ??= $this->min;
        $max ??= $this->max;

        $username = trim($username);
        if (!$username) {
            return false;
        }

        $length = mb_strlen($username);
        if ($length < $min || $length > $max) {
            $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];
            $this->error = $this->errors['min_max'];

            return false;
        }

        if (!preg_match('/^[a-z0-9\._\-]+$/i', $username)) {
            $this->error = $this->errors['chars'];
            return false;
        }

        return $this->app->plugins->filter('validate.username', true, $username, $min, $max, $this);
    }
}
