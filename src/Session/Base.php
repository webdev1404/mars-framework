<?php
/**
* The Session Driver Base Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App\Kernel;

/**
 * The Session Driver Base Class
 */
abstract class Base implements SessionInterface
{
    use Kernel;

    /**
     * @var string $prefix Prefix to apply to all session keys
     */
    protected string $prefix {
        get {
            if (isset($this->prefix)) {
                return $this->prefix;
            }

            $this->prefix = $this->app->config->session->prefix;

            if ($this->prefix) {
                $this->prefix .= '-';
            }

            return $this->prefix;
        }
    }

    /**
     * @see SessionInterface::start()
     * {@inheritdoc}
     */
    public function start()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return;
        }
        
        session_start();
    }

    /**
     * @see SessionInterface::delete()
     * {@inheritdoc}
     */
    public function delete()
    {
        session_unset();
        session_destroy();
    }

    /**
     * @see SessionInterface::getId()
     * {@inheritdoc}
     */
    public function getId() : string
    {
        return session_id();
    }

    /**
     * @see SessionInterface::regenerateId()
     * {@inheritdoc}
     */
    public function regenerateId() : string
    {
        session_regenerate_id(true);

        return $this->getId();
    }

    /**
     * @see SessionInterface::isSet()
     * {@inheritdoc}
     */
    public function isSet(string $name) : bool
    {
        $key = $this->prefix . $name;

        return isset($_SESSION[$key]);
    }

    /**
     * @see SessionInterface::get()
     * {@inheritdoc}
     */
    public function get(string $name, bool $unserialize = false, mixed $default = null) : mixed
    {
        $key = $this->prefix . $name;
        
        if (!isset($_SESSION[$key])) {
            return $default;
        }

        $value = $_SESSION[$key];

        if ($unserialize) {
            return $this->app->serializer->unserialize($value, [], false);
        }

        return $value;
    }

    /**
     * @see SessionInterface::set()
     * {@inheritdoc}
     */
    public function set(string $name, mixed $value, bool $serialize = false)
    {
        $key = $this->prefix . $name;

        if ($serialize) {
            $value = $this->app->serializer->serialize($value, false);
        }

        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * @see SessionInterface::unset()
     * {@inheritdoc}
     */
    public function unset(string $name)
    {
        $key = $this->prefix . $name;

        unset($_SESSION[$key]);
    }
}
