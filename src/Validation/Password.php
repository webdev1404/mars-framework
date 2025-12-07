<?php
/**
* The Password Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Password Validator Class
 */
class Password extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'validate.password';

    public array $errors = [
        'min_max' => 'validate.password.length',
        'chars' => 'validate.password.chars',
    ];

    protected int $min = 6;
    protected int $max = 100;

    /**
     * Validates a password
     * @param string $password The password
     * @return bool
     */
    public function isValid(string $password, ?int $min = null, ?int $max = null) : bool
    {
        $min ??= $this->min;
        $max ??= $this->max;

        $password = trim($password);
        if (!$password) {
            return false;
        }

        $length = mb_strlen($password);
        if ($length < $min || $length > $max) {
            $this->error_replacements = ['{MIN}' => $min, '{MAX}' => $max];
            $this->error = $this->errors['min_max'];

            return false;
        }

        return $this->app->plugins->filter('validate_password', true, $password, $min, $max, $this);
    }
}
