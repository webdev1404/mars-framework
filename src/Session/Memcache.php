<?php
/**
* The Memcache Session Class
* @package Mars
*/

namespace Mars\Session;

/**
 * The Memcache Session Class
 * Session driver which uses the memcache
 */
class Memcache extends Base implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    /**
     * @var int $lifetime The session's lifetime
     */
    protected int $lifetime {
        get {
            if (isset($this->lifetime)) {
                return $this->lifetime;
            }

            $this->lifetime = ini_get('session.gc_maxlifetime');

            return $this->lifetime;
        }
    }

    /**
     * @see SessionInterface::start()
     */
    public function start()
    {
        if (!$this->app->memcache->enabled) {
            throw new \Exception('Memcache must be enabled to be able to use the session memcache driver');
        }

        session_set_save_handler($this);

        session_start();
    }

    /**
     * Initialize the session
     * @see \SessionHandler::open()
     * {@inheritDoc}
     */
    public function open(string $path, string $name) : bool
    {
        return true;
    }

    /**
     * Closes the session
     * @see \SessionHandler::close()
     * {@inheritDoc}
     */
    public function close() : bool
    {
        return true;
    }

    /**
     * Reads the session data
     * @see \SessionHandler::read()
     * {@inheritDoc}
     */
    public function read(string $id) : string|false
    {
        $data = $this->app->memcache->get("session-{$id}");
        
        return $data ?? '';
    }

    /**
     * Writes the session data
     * @see \SessionHandler::write()
     * {@inheritDoc}
     */
    public function write(string $id, string $data) :  bool
    {
        return $this->app->memcache->set("session-{$id}", $data, false, $this->lifetime);
    }

    /**
     * Destroys the session data
     * @see \SessionHandler::destroy()
     * {@inheritDoc}
     */
    public function destroy(string $id) : bool
    {
        return $this->app->memcache->delete("session-{$id}");
    }

    /**
     * Deletes expired sessions
     * @see \SessionHandler::gc()
     * {@inheritDoc}
     */
    public function gc(int $maxlifetime) : int|false
    {
        return 0;
    }

    /**
     * @see \SessionUpdateTimestampHandlerInterface::validateId()
     * {@inheritDoc}
     */
    public function validateId(string $id) : bool
    {
        return $this->app->memcache->exists("session-{$id}");
    }

    /**
     * Updates the timestamp of a session when its data didn't change
     * @see \SessionUpdateTimestampHandlerInterface::updateTimestamp()
     * {@inheritDoc}
     */
    public function updateTimestamp(string $id, string $data) : bool
    {
        return $this->write($id, $data);
    }
}
