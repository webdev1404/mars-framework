<?php
/**
* The Serializer Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Serializers\DriverInterface;

/**
 * The Serializer Class
 * Serializes/Unserializes data
 * Change the driver only if you know what you're doing! Preferably at installation time. You might try to unserialize data which has been serialized with a different driver, otherwise
 */
class Serializer
{
    use InstanceTrait;
    
    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'php' => \Mars\Serializers\Php::class,
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

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'serializer', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    public protected(set) DriverInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->serializer_driver);

            return $this->driver;
        }
    }

    /**
     * protected DriverInterface $php_driver The php driver
     */
    public protected(set) DriverInterface $php_driver {
        get {
            if (isset($this->php_driver)) {
                return $this->php_driver;
            }

            $this->php_driver = $this->driver;
            if ($this->app->config->serializer_driver != 'php') {
                $this->php_driver = $this->drivers->get('php');
            }

            return $this->php_driver;
        }
    }

    /**
     * Returns the driver used to serialize/unserialize
     * @param bool $use_php_driver If true, will always serialize using the php driver
     * @return DriverInterface The driver
     */
    protected function getDriver(bool $use_php_driver) : DriverInterface
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
    public function serialize($data, bool $encode = true, bool $use_php_driver = true) : string
    {
        $data = $this->getDriver($use_php_driver)->serialize($data);

        if ($encode) {
            $data = base64_encode($data);
        }

        return $data;
    }

    /**
     * Unserializes data
     * @param mixed $data The data to unserialize
     * @param mixed $default_value The default value to return if $data is an empty string or null
     * @param bool $decode If true, will base64 decode the serialize data
     * @param bool $use_php_driver If true, will always unserialize using the php driver
     * @return mixed The unserialized data
     */
    public function unserialize(?string $data, $default_value = [], bool $decode = true, bool $use_php_driver = true)
    {
        if ($data === '' || $data === null) {
            return $default_value;
        }

        if ($decode) {
            $data = base64_decode($data);
        }

        return $this->getDriver($use_php_driver)->unserialize($data);
    }
}
