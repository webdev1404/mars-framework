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
     * @var string $hash_algo The default hashing algorithm
     */
    protected string $hash_algo {
        get => $this->app->config->security->hash_algo;
    }

    /**
     * @var string $strong_hash_algo The strong hashing algorithm
     */
    protected string $strong_hash_algo {
        get => $this->app->config->security->strong_hash_algo;
    }

    /**
     * Hashes data using the security.hash.algo algorithm
     * @param string $data The data to hash
     * @return string The hashed data
     */
    public function getHash(string $data) : string
    {
        return hash($this->hash_algo, $data);
    }

    /**
     * Verifies a hash against data
     * @param string $data The data to verify
     * @param string $hash The hash to verify against
     * @return bool True if the data matches the hash, false otherwise
     */
    public function verifyHash(string $data, string $hash) : bool
    {
        return hash($this->hash_algo, $data) === $hash;
    }

    /**
     * Hashes data using the security.strong_hash.algo algorithm
     * @param string $data The data to hash
     * @return string The hashed data
     */
    public function getStrongHash(string $data) : string
    {
        return hash($this->strong_hash_algo, $data);
    }

    /**
     * Verifies a strong hash against data
     * @param string $data The data to verify
     * @param string $hash The hash to verify against
     * @return bool True if the data matches the hash, false otherwise
     */
    public function verifyStrongHash(string $data, string $hash) : bool
    {
        return hash($this->strong_hash_algo, $data) === $hash;
    }

    /**
     * Returns a token from a string
     * @param string $string The string to generate the token from
     * @return string The generated token
     */
    public function getToken(string $string) : string
    {
        return $this->hashPassword($string);
    }

    /**
     * Verifies a token against a string
     * @param string $string The string to verify
     * @param string $token The token to verify against
     * @return bool True if the string matches the token, false otherwise
     */
    public function verifyToken(string $string, string $token) : bool
    {
        return $this->verifyPassword($string, $token);
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
