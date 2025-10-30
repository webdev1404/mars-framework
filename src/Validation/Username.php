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
     * {@inheritdoc}
     */
    public string $error = 'error.validate_username';

    public array $errors = [
        'min_max' => 'error.validate_username_length',
        'chars' => 'error.validate_username_chars',
    ];

    protected int $min = 5;
    protected int $max = 100;

    /**
     * Validates a username
     * @param string $username The username
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

        if (!preg_match('/^[a-z0-9\._-]*$/i', $username)) {
            $this->error = $this->errors['chars'];
            return false;
        }

        return $this->app->plugins->filter('validate_username', true, $username, $min, $max, $this);
    }
}
