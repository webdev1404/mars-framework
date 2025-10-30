<?php
/**
* The Captcha Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Captcha Validator Class
 * Validates a captcha
 */
class Captcha extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'error.validate_captcha';

    /**
     * Validates a captcha
     * @param string $value The value
     * @return bool
     */
    public function isValid(string $value) : bool
    {
        return $this->app->captcha->check();
    }
}
