<?php
/**
* The Session Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Session\DriverInterface;

/**
 * The Session Class
 * The system's session object
 */
class Session
{
    use InstanceTrait;
    
    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'session', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    public protected(set) DriverInterface $driver;

    /**
     * @var string $prefix Prefix to apply to all session keys
     */
    protected string $prefix {
        get {
            if (isset($this->prefix)) {
                return $this->prefix;
            }

            $this->prefix = $this->app->config->session_prefix;

            if ($this->prefix) {
                $this->prefix .= '-';
            }

            return $this->prefix;
        }
    }

    /**
     * @var bool $started True if the session has been started
     */
    protected bool $started = false;

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'php' => \Mars\Session\Php::class,
        'db' => \Mars\Session\Db::class,
        'memcache' => \Mars\Session\Memcache::class
    ];

    /**
     * Starts the session
     */
    public function start()
    {
        session_start();

        $this->started = true;

        //get the driver manually, since we're not using it per se
        $this->driver = $this->drivers->get($this->app->config->session_driver);
    }

    /**
     * Deletes the session cookie
     * @return static
     */
    public function delete() : static
    {
        session_unset();
        session_destroy();

        return $this;
    }


    /**
     * Returns the session id
     * @return string The session id
     */
    public function getId() : string
    {
        if (!$this->started) {
            $this->start();
        }

        return session_id();
    }

    /**
     * Regenerates the session id
     * @return string The new session id
     */
    public function regenerateId() : string
    {
        if (!$this->started) {
            $this->start();
        }
        session_regenerate_id();

        return $this->getId();
    }

    /**
     * Determines if $_SESSION[$name] is set
     * @param string $name The name of the var
     * @return bool Returns true if $_SESSION[$name] is set, false otherwise
     */
    public function isSet(string $name) : bool
    {
        if (!$this->started) {
            $this->start();
        }

        $key = $this->prefix . $name;

        return isset($_SESSION[$key]);
    }

    /**
     * Returns $_SESSION[$name] if set
     * @param string $name The name of the var
     * @param bool $unserialize If true, will unserialize the returned result
     * @param mixed $not_set The return value, if $_SESSION[$name] isn't set
     * @return mixed Will return null if the session is not enabled
     */
    public function get(string $name, bool $unserialize = false, $not_set = null)
    {
        if (!$this->started) {
            $this->start();
        }

        $key = $this->prefix . $name;

        if (!isset($_SESSION[$key])) {
            return $not_set;
        }

        $value = $_SESSION[$key];

        if ($unserialize) {
            return $this->app->serializer->unserialize($value, [], false);
        }

        return $value;
    }

    /**
     * Sets a session value
     * @param string $name The name of the var
     * @param mixed $value The value
     * @param bool $serialize If true, will serialize the value
     * @return static
     */
    public function set(string $name, $value, bool $serialize = false) : static
    {
        if (!$this->started) {
            $this->start();
        }

        $key = $this->prefix . $name;

        if ($serialize) {
            $value = $this->app->serializer->serialize($value, false);
        }

        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Unsets a session value
     * @param string $name The name of the var
     * @return static
     */
    public function unset(string $name) : static
    {
        if (!$this->started) {
            $this->start();
        }

        $key = $this->prefix . $name;

        unset($_SESSION[$key]);

        return $this;
    }
}
