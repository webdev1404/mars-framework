<?php
/**
* The PDO Database Driver
* @package Mars
*/

namespace Mars\Db\Drivers;

use Mars\Db\DbInterface;

/**
 * The PDO Database Driver
 */
abstract class Pdo implements DbInterface
{
    /**
     * @var PDO $handle The PDO handle
     */
    protected ?\PDO $handle = null;

    /**
     * @var object The result of the last query operation
     */
    protected ?\PDOStatement $result = null;

    /**
     * @see DbInterface::connect()
     * {@inheritDoc}
     */
    public function connect(string $hostname, string $port, #[\SensitiveParameter] string $username, #[\SensitiveParameter] string $password, string $database, bool $persistent, string $charset)
    {
        $dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset={$charset}";
        $options = [\PDO::ATTR_PERSISTENT => $persistent, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
        
        try {
            $this->handle = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception('PDO Error: ' . $e->getMessage());
        }
    }

    /**
     * @see DbInterface::disconnect()
     * {@inheritDoc}
     */
    public function disconnect()
    {
        if (isset($this->handle)) {
            unset($this->handle);
        }
    }

    /**
     * @see DbInterface::getIterator()
     * {@inheritDoc}
     */
    public function getIterator($result) : \Iterator
    {
        return $result->getIterator();
    }

    /**
     * @see DbInterface::quote()
     * {@inheritDoc}
     */
    public function quote(string $string) : string
    {
        return $this->handle->quote($string);
    }
    
    /**
     * @see DbInterface::beginTransaction()
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        $this->handle->beginTransaction();
    }
    
    /**
     * @see DbInterface::commit()
     * {@inheritDoc}
     */
    public function commit()
    {
        $this->handle->commit();
    }
    
    /**
     * @see DbInterface::rollback()
     * {@inheritDoc}
     */
    public function rollback()
    {
        $this->handle->rollBack();
    }

    /**
     * @see DbInterface::query()
     * {@inheritDoc}
     */
    public function query(string $sql, array $params = []) : ?object
    {
        try {
            if ($params) {
                $this->result = $this->handle->prepare($sql);
                $this->result->execute($this->getParams($params));
                $this->result->setFetchMode(\PDO::FETCH_OBJ);
            } else {
                $this->result = $this->handle->query($sql);
                $this->result->setFetchMode(\PDO::FETCH_OBJ);
            }
        } catch (\PDOException $e) {
            $this->result = null;

            throw new \Exception('PDO Error: ' . $e->getMessage());
        }

        return $this->result;
    }

    /**
     * Returns the prepared params
     * @param array $params The params
     * @return array The prepared params
     */
    protected function getParams(array $params) : array
    {
        $keys = array_map(function ($key) {
            return ':' . $key;
        }, array_keys($params));

        return array_combine($keys, $params);
    }

    /**
     * @see DbInterface::free()
     * {@inheritDoc}
     */
    public function free(object $result)
    {
        unset($result);
    }

    /**
     * @see DbInterface::lastId()
     * {@inheritDoc}
     */
    public function lastId() : int
    {
        return $this->handle->lastInsertId();
    }

    /**
     * @see DbInterface::affectedRows()
     * {@inheritDoc}
     */
    public function affectedRows() : int
    {
        return $this->result->rowCount();
    }

    /**
     * @see DbInterface::numRows()
     * {@inheritDoc}
     */
    public function numRows(object $result) : int
    {
        return $result->rowCount();
    }

    /**
     * @see DbInterface::fetchArray()
     * {@inheritDoc}
     */
    public function fetchArray(object $result) : ?array
    {
        return $result->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @see DbInterface::fetchRow()
     * {@inheritDoc}
     */
    public function fetchRow(object $result) : ?array
    {
        return $result->fetch(\PDO::FETCH_NUM) ?: null;
    }

    /**
     * @see DbInterface::fetchObject()
     * {@inheritDoc}
     */
    public function fetchObject(object $result, string $class_name = '') : ?object
    {
        if (!$class_name) {
            $class_name = '\stdClass';
        }

        return $result->fetchObject($class_name) ?: null;
    }
    
    /**
     * @see DbInterface::fetchColumn()
     * {@inheritDoc}
     */
    public function fetchColumn(object $result, int $column = 0) : ?string
    {
        return $result->fetchColumn($column) ?: null;
    }

    /**
     * @see DbInterface::fetchAll()
     * {@inheritDoc}
     */
    public function fetchAll(object $result, bool|string $class_name = '') : array
    {
        if ($class_name === true) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        if (!$class_name) {
            $class_name = '\stdClass';
        }

        return $result->fetchAll(\PDO::FETCH_CLASS, $class_name);
    }
    
    /**
     * @see DbInterface::fetchAllFromColumn()
     * {@inheritDoc}
     */
    public function fetchAllFromColumn(object $result, int $column = 0) : array
    {
        return $result->fetchAll(\PDO::FETCH_COLUMN, $column);
    }
}
