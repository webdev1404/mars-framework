<?php
/**
* The Ip Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Ip Validator Class
 */
class Ip extends Rule
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'validate.ip';

    /**
     * Checks if $ip is a valid IP address
     * @param string $value The IP to validate
     * @return bool Returns true if the IP is valid
     */
    public function isValid(string $value) : bool
    {
        $ip = trim($value);

        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
