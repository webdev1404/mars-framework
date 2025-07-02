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
     * Checks the captcha is correct
     * @return bool Returns bool if the captcha is correct
     */
    public function check() : bool;

    /**
     * Outputs the captcha
     */
    public function output();
}
