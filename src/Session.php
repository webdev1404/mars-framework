<?php
/**
* The Session Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Session\SessionInterface;

/**
 * The Session Class
 * The system's session object
 */
class Session
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'php' => \Mars\Session\Php::class,
        'db' => \Mars\Session\Db::class,
        'memcache' => \Mars\Session\Memcache::class
    ];
    
    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, SessionInterface::class, 'session', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var SessionInterface $driver The driver object
     */
    public protected(set) SessionInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->session->driver);
            $this->driver->start();

            return $this->driver;
        }
    }

    /**
     * @var string $token The session CSRF token value
     */
    public protected(set) ?string $token {
        get {
            if (isset($this->token)) {
                return $this->token;
            }

            $this->token = $this->get('token_csrf');
            if (!$this->token) {
                $this->token = $this->app->random->getString(32);
                $this->set('token_csrf', $this->token);
            }

            return $this->token;
        }
    }

    /**
     * Destroys the session and unsets all session variables
     * @return static
     */
    public function destroy() : static
    {
        $this->driver->delete();

        return $this;
    }

    /**
     * Returns the session id
     * @return string The session id
     */
    public function getId() : string
    {
        return $this->driver->getId();
    }

    /**
     * Regenerates the session id
     * @return string The new session id
     */
    public function regenerateId() : string
    {
        return $this->driver->regenerateId();
    }

    /**
     * Determines if $_SESSION[$name] is set
     * @param string $name The name of the var
     * @return bool Returns true if $_SESSION[$name] is set, false otherwise
     */
    public function isSet(string $name) : bool
    {
        return $this->driver->isSet($name);
    }

    /**
     * Returns $_SESSION[$name] if set
     * @param string $name The name of the var
     * @param bool $unserialize If true, will unserialize the returned result
     * @param mixed $default The return value, if $_SESSION[$name] isn't set
     * @return mixed Will return null if the session is not enabled
     */
    public function get(string $name, bool $unserialize = false, mixed $default = null) : mixed
    {
        return $this->driver->get($name, $unserialize, $default);
    }

    /**
     * Sets a session value
     * @param string $name The name of the var
     * @param mixed $value The value
     * @param bool $serialize If true, will serialize the value
     * @return static
     */
    public function set(string $name, mixed $value, bool $serialize = false) : static
    {
        $this->driver->set($name, $value, $serialize);

        return $this;
    }

    /**
     * Unsets a session value
     * @param string $name The name of the var
     * @return static
     */
    public function unset(string $name) : static
    {
        $this->driver->unset($name);

        return $this;
    }
}
