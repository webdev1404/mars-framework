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
     * {@inheritDoc}
     */
    public function encrypt(string $key, string $data): array
    {
        $this->checkKey($key);

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($data, $nonce, $key);

        return [base64_encode($nonce), base64_encode($ciphertext)];
    }

    /**
     * @see CryptInterface::decrypt()
     * {@inheritDoc}
     */
    public function decrypt(string $key, string $nonce, string $data): string
    {
        $this->checkKey($key);

        $nonce = base64_decode($nonce, true);
        $ciphertext = base64_decode($data, true);
        if (!$nonce || strlen($nonce) !== SODIUM_CRYPTO_SECRETBOX_NONCEBYTES || !$ciphertext) {
            throw new \Exception('Invalid data provided for decryption with the sodium driver');
        }

        $value = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        if (!$value) {
            throw new \Exception('Decryption failed using sodium_crypto_secretbox_open');
        }

        return $value;
    }

    /**
     * Check that the key length is valid
     * @param string $key The encryption key
     * @throws \Exception If the key length is invalid
     */
    protected function checkKey(string $key)
    {
        if (strlen($key) != SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new \Exception('The crypt key must be ' . SODIUM_CRYPTO_SECRETBOX_KEYBYTES . ' bytes long for the sodium driver');
        }
    }
}
