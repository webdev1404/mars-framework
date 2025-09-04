<?php
/**
* The Crypt Driver Interface
* @package Mars
*/

namespace Mars\Crypt;

/**
 * The Crypt Driver Interface
 */
interface CryptInterface
{
    /**
     * Encrypts the given data
     * @param string $data The data to encrypt
     * @param string $key The key to use for encryption
     * @return array The encrypted data
     */
    public function encrypt(string $data, string $key): array;

    /**
     * Decrypts the given data
     * @param string $key The key to use for decryption
     * @param string $iv The initialization vector used for encryption
     * @param string $data The data to decrypt
     * @return string The decrypted data
     */
    public function decrypt(string $key, string $iv, string $data): string;
}
