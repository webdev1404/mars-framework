<?php
/**
* The Crypt Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Crypt\CryptInterface;

/**
 * The Crypt Class
 * Handles the interactions with the cryptography system.
 */
class Crypt
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'openssl' => \Mars\Crypt\OpenSSL::class,
        'sodium' => \Mars\Crypt\Sodium::class
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, CryptInterface::class, 'crypt', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var CryptInterface $driver The driver object
     */
    public protected(set) ?CryptInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->crypt->driver);

            return $this->driver;
        }
    }

    /**
     * @var array $keys Secret keys used for data encryption and decryption
     */
    protected array $keys {
        get {
            if (isset($this->keys)) {
                return $this->keys;
            }

            $this->keys = $this->app->config->crypt->keys;
            if (!$this->keys) {
                throw new \Exception('No crypt.keys defined in the configuration. Please add encryption keys to your configuration file.');
            }
            if (array_is_list($this->keys)) {
                throw new \Exception('The crypt.keys config option must be an associative array');
            }

            return $this->keys;
        }
    }

    /**
     * @var string $key_index The index of the key in use (the last one in the list)
     */
    protected string $key_index {
        get => array_key_last($this->keys);
    }

    /**
     * @var string $key The key in use
     */
    protected string $key {
        get {
            if (isset($this->key)) {
                return $this->key;
            }

            $this->key = $this->keys[$this->key_index];

            return $this->key;
        }
    }

    /**
     * Encrypts the given data
     * @param mixed $data The data to encrypt
     * @return string The encrypted data
     * @throws \Exception If encryption fails
     */
    public function encrypt(mixed $data): string
    {
        $data_type = 's';
        if (!is_string($data)) {
            $data_type = 'o';
            $data = serialize($data);
        }

        [$iv, $ciphertext] = $this->driver->encrypt($this->key, $data);
        
        $parts = [$this->key_index, $data_type, $iv, $ciphertext];

        return implode(':::', $parts);
    }

    /**
     * Decrypts the given data
     * @param string $data The data to decrypt
     * @return mixed The decrypted data
     * @throws \Exception If decryption fails
     */
    public function decrypt(string $data): mixed
    {
        $parts = explode(':::', $data);
        if (count($parts) != 4) {
            return null;
        }
        
        $key_index = $parts[0];
        $key = $this->keys[$key_index] ?? null;
        if (!$key) {
            return null;
        }

        $string = $this->driver->decrypt($key, $parts[2], $parts[3]);
        if ($string === null) {
            return null;
        }

        if ($parts[1] == 'o') {
            $string = unserialize($string);
        }

        return $string;
    }
}
