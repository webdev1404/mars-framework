<?php
/**
* The Captcha Driver Interface
* @package Mars
*/

namespace Mars\Captcha;

/**
 * The Captcha Driver Interface
 */
interface CaptchaInterface
{
    /**
     * Verifies the captcha
     * @return bool True if the captcha is valid, false otherwise
     */
    public function verify() : bool;

    /**
     * Renders the captcha
     */
    public function render();
}
