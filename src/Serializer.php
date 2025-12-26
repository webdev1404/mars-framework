<?php
/**
* The Serializer Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Serializers\SerializerInterface;

/**
 * The Serializer Class
 * Serializes/Unserializes data
 * Change the driver only if you know what you're doing! Preferably at installation time. You might try to unserialize data which has been serialized with a different driver, otherwise
 */
class Serializer
{
    use Kernel;
    
    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'php' => \Mars\Serializers\Php::class,
        'json' => \Mars\Serializers\Json::class,
        'igbinary' => \Mars\Serializers\Igbinary::class,
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, SerializerInterface::class, 'serializer', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var SerializerInterface $driver The driver object
     */
    public protected(set) SerializerInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->serializer->driver);

            return $this->driver;
        }
    }

    /**
     * @var SerializerInterface $php_driver The php driver
     */
    public protected(set) SerializerInterface $php_driver {
        get {
            if (isset($this->php_driver)) {
                return $this->php_driver;
            }

            $this->php_driver = $this->driver;
            if ($this->app->config->serializer->driver != 'php') {
                $this->php_driver = $this->drivers->get('php');
            }

            return $this->php_driver;
        }
    }

    /**
     * Returns the driver used to serialize/unserialize
     * @param bool $use_php_driver If true, will always serialize using the php driver
     * @return SerializerInterface The driver
     */
    protected function getDriver(bool $use_php_driver) : SerializerInterface
    {
        if ($use_php_driver) {
            return $this->php_driver;
        }

        return $this->driver;
    }

    /**
     * Serializes data
     * @param mixed $data The data to serialize
     * @param bool $encode If true, will base64 encode the serialize data
     * @param bool $use_php_driver If true, will always serialize using the php driver
     * @return string The serialized data
     */
    public function serialize(mixed $data, bool $encode = false, bool $use_php_driver = true) : string
    {
        $data = $this->getDriver($use_php_driver)->serialize($data);

        if ($encode) {
            $data = base64_encode($data);
        }

        return $data;
    }

    /**
     * Unserializes data
     * @param string|null $data The data to unserialize
     * @param mixed $default_value The default value to return if $data is an empty string or null
     * @param bool $decode If true, will base64 decode the serialize data
     * @param bool $use_php_driver If true, will always unserialize using the php driver
     * @return mixed The unserialized data
     */
    public function unserialize(?string $data, $default_value = [], bool $decode = false, bool $use_php_driver = true)
    {
        if ($data === '' || $data === null) {
            return $default_value;
        }

        if ($decode) {
            $data = base64_decode($data);
            if ($data === false) {
                return $default_value;
            }
        }

        return $this->getDriver($use_php_driver)->unserialize($data);
    }

    /**
     * Serializes data using the current driver
     * @param mixed $data The data to serialize
     * @param bool $encode If true, will base64 encode the serialize data
     * @return string The serialized data
     */
    public function serializeData(mixed $data, bool $encode = false) : string
    {
        return $this->serialize($data, $encode, false);
    }

    /**
     * Unserializes data using the current driver
     * @param string|null $data The data to unserialize
     * @param mixed $default_value The default value to return if $data is an empty string or null
     * @param bool $decode If true, will base64 decode the serialize data
     * @return mixed The unserialized data
     */
    public function unserializeData(?string $data, $default_value = [], bool $decode = false)
    {
        return $this->unserialize($data, $default_value, $decode, false);
    }
}
