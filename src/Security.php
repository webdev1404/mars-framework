<?php
/**
* The Security Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Security Class
 * Handles security-related tasks
 */
class Security
{
    use Kernel;

    /**
     * Hashes a password
     * @param string $password The password to hash
     * @return string The hashed password
     */
    public function hashPassword(string $password) : string
    {
        return \password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verifies a password against a hash
     * @param string $password The password to verify
     * @param string $hash The hash to verify against
     * @return bool True if the password matches the hash, false otherwise
     */
    public function verifyPassword(string $password, string $hash) : bool
    {
        return \password_verify($password, $hash);
    }

    /**
     * Checks if a password hash needs to be rehashed
     * @param string $hash The hash to check
     * @return bool True if the hash needs to be rehashed, false otherwise
     */
    public function passwordNeedsRehash(string $hash) : bool
    {
        return \password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}
