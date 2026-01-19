<?php
/**
* The Db Session Class
* @package Mars
*/

namespace Mars\Session;

/**
 * The Db Session Class
 * Session driver which uses the database
 * The table must be created with the following SQL:
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `data` text,
    `timestamp` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `timestamp_idx` (`timestamp`)
);
 */
class Db extends Base implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    /**
     * @var string $table The table where the sessions are stored
     */
    protected string $table {
        get => $this->app->config->session->table;
    }

    /**
     * @see SessionInterface::start()
     */
    public function start()
    {
        if (!$this->table) {
            throw new \Exception('The database table where the session data is stored is not set in config.session.table');
        }

        session_set_save_handler($this);

        session_start();
    }

    /**
     * Initialize the session
     * @see \SessionHandler::open()
     * {@inheritdoc}
     */
    public function open(string $path, string $name) : bool
    {
        return true;
    }

    /**
     * Closes the session
     * @see \SessionHandler::close()
     * {@inheritdoc}
     */
    public function close() : bool
    {
        return true;
    }

    /**
     * Reads the session data
     * @see \SessionHandler::read()
     * {@inheritdoc}
     */
    public function read(string $id) : string|false
    {
        $data = $this->app->db->selectResult($this->table, 'data', ['id' => $id]);
        
        return $data ?? '';
    }

    /**
     * Writes the session data
     * @see \SessionHandler::write()
     * {@inheritdoc}
     */
    public function write(string $id, string $data) :  bool
    {
        $values = [
            'id' => $id,
            'timestamp' => time(),
            'data' => $data
        ];

        $this->app->db->replace($this->table, $values);

        return true;
    }

    /**
     * Destroys the session data
     * @see \SessionHandler::destroy()
     * {@inheritdoc}
     */
    public function destroy(string $id) : bool
    {
        $this->app->db->deleteById($this->table, $id);

        return true;
    }

    /**
     * Deletes expired sessions
     * @see \SessionHandler::gc()
     * {@inheritdoc}
     */
    public function gc(int $maxlifetime) : int|false
    {
        $cutoff = time() - $maxlifetime;

        return $this->app->db->query("DELETE FROM {$this->table} WHERE `timestamp` < {$cutoff}")->affectedRows();
    }

    /**
     * @see \SessionUpdateTimestampHandlerInterface::validateId()
     * {@inheritdoc}
     */
    public function validateId(string $id) : bool
    {
        return $this->app->db->exists($this->table, ['id' => $id]);
    }

    /**
     * Updates the timestamp of a session when its data didn't change
     * @see \SessionUpdateTimestampHandlerInterface::updateTimestamp()
     * {@inheritdoc}
     */
    public function updateTimestamp(string $id, string $data) : bool
    {
        $this->app->db->update($this->table, ['timestamp' => time()], ['id' => $id]);

        return true;
    }
}
