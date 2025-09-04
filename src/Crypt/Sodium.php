<?php
/**
* The Sodium Crypt Class
* @package Mars
*/

namespace Mars\Crypt;

/**
 * The Sodium Crypt Class
 * Crypt driver which uses Sodium
 */
class Sodium implements CryptInterface
{
    /**
     * @see CryptInterface::encrypt()
     * {@inheritdoc}
     */
    public function encrypt(string $data, string $key): array
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($data, $nonce, $key);

        return [base64_encode($nonce), base64_encode($ciphertext)];
    }

    /**
     * @see CryptInterface::decrypt()
     * {@inheritdoc}
     */
    public function decrypt(string $key, string $iv, string $data): string
    {
        $nonce = base64_decode($iv);
        $ciphertext = base64_decode($data);

        return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    }
}
