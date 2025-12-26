<?php
/**
* The Session Driver Interface
* @package Mars
*/

namespace Mars\Session;

/**
 * The Session Driver Interface
 */
interface SessionInterface
{
    /**
     * Starts the session
     */
    public function start();

    /**
     * Destroys the session and unsets all session variables
     */
    public function delete();

    /**
     * Returns the session id
     * @return string The session id
     */
    public function getId() : string;

    /**
     * Regenerates the session id
     * @return string The new session id
     */
    public function regenerateId() : string;

    /**
     * Determines if $_SESSION[$name] is set
     * @param string $name The name of the var
     * @return bool Returns true if $_SESSION[$name] is set, false otherwise
     */
    public function isSet(string $name) : bool;

    /**
     * Returns the value of $_SESSION[$name] if set
     * @param string $name The name of the var
     * @param bool $unserialize If true, will unserialize the returned result
     * @param mixed $default The return value, if $_SESSION[$name] isn't set
     * @return mixed The value of $_SESSION[$name] or $default if not set
     */
    public function get(string $name, bool $unserialize = false, mixed $default = null) : mixed;

    /**
     * Sets a session value
     * @param string $name The name of the var
     * @param mixed $value The value
     * @param bool $serialize If true, will serialize the value
     */
    public function set(string $name, mixed $value, bool $serialize = false);

    /**
     * Unsets a session value
     * @param string $name The name of the var
     */
    public function unset(string $name);
}
