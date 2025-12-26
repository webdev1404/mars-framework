<?php
/**
* The OpenSSL Crypt Class
* @package Mars
*/

namespace Mars\Crypt;

/**
 * The OpenSSL Crypt Class
 * Crypt driver which uses OpenSSL
 */
class OpenSSL implements CryptInterface
{
    /**
     * @var string $algo The encryption algorithm to use
     */
    protected string $algo = 'aes-256-cbc';


    /**
     * @see CryptInterface::encrypt()
     * {@inheritdoc}
     */
    public function encrypt(string $key, string $data): array
    {
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($data, $this->algo, $key, OPENSSL_RAW_DATA, $iv);
        if (!$ciphertext) {
            throw new \Exception('Encryption failed using openssl_encrypt');
        }

        return [base64_encode($iv), base64_encode($ciphertext)];
    }

    /**
     * @see CryptInterface::decrypt()
     * {@inheritdoc}
     */
    public function decrypt(string $key, string $iv, string $data): string
    {
        $ciphertext = base64_decode($data);
        $iv = base64_decode($iv);
        if (!$ciphertext || !$iv) {
            throw new \Exception('Invalid data provided for decryption with the openssl driver');
        }

        $value = openssl_decrypt($ciphertext, $this->algo, $key, OPENSSL_RAW_DATA, $iv);
        if (!$value) {
            throw new \Exception('Decryption failed using openssl_decrypt');
        }

        return $value;
    }
}
