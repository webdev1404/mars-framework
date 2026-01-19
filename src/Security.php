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
     * @var string $default_algo The default hashing algorithm
     */
    protected string $default_algo = 'sha512';

    /**
     * Hashes data using the specified algorithm
     * @param string $data The data to hash
     * @param string|null $algo The hashing algorithm to use. If null, the default algorithm (sha512) will be used
     * @return string The hashed data
     */
    public function hash(string $data, ?string $algo = null) : string
    {
        return hash($algo ?? $this->default_algo, $data);
    }

    /**
     * Verifies a hash against data using the specified algorithm
     * @param string $data The data to verify
     * @param string $hash The hash to verify against
     * @param string|null $algo The hashing algorithm to use. If null, the default algorithm (sha512) will be used
     * @return bool True if the data matches the hash, false otherwise
     */
    public function verifyHash(string $data, string $hash, ?string $algo = null) : bool
    {
        return hash($algo ?? $this->default_algo, $data) === $hash;
    }

    /**
     * Returns a token from a string
     * @param string $string The string to generate the token from
     * @param string|null $algo The hashing algorithm to use
     */
    public function getToken(string $string, ?string $algo = null) : string
    {
        return $algo ? $this->hash($string, $algo) : $this->hashPassword($string);
    }

    /**
     * Verifies a token against a string
     * @param string $string The string to verify
     * @param string $token The token to verify against
     * @param string|null $algo The hashing algorithm to use
     * @return bool True if the string matches the token, false otherwise
     */
    public function verifyToken(string $string, string $token, ?string $algo = null) : bool
    {
        return $algo ? $this->verifyHash($string, $token, $algo) : $this->verifyPassword($string, $token);
    }

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
