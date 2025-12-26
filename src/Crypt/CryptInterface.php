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
     * @param string $key The key to use for encryption
     * @param string $data The data to encrypt
     * @return array{iv: string, data: string} The encrypted data, with 'iv' (initialization vector) and 'data' (encrypted payload) keys
     * @throws \Exception If encryption fails
     */
    public function encrypt(string $key, string $data): array;

    /**
     * Decrypts the given data
     * @param string $key The key to use for decryption
     * @param string $iv The initialization vector used for encryption
     * @param string $data The data to decrypt
     * @return string The decrypted data
     * @throws \Exception If decryption fails
     */
    public function decrypt(string $key, string $iv, string $data): string;
}
