<?php
/**
* The PDO Database Driver
* @package Mars
*/

namespace Mars\Db;

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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if (isset($this->handle)) {
            unset($this->handle);
        }
    }

    /**
     * @see DbInterface::getIterator()
     * {@inheritdoc}
     */
    public function getIterator($result) : \Iterator
    {
        return $result->getIterator();
    }

    /**
     * @see DbInterface::quote()
     * {@inheritdoc}
     */
    public function quote(string $string) : string
    {
        return $this->handle->quote($string);
    }
    
    /**
     * @see DbInterface::beginTransaction()
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->handle->beginTransaction();
    }
    
    /**
     * @see DbInterface::commit()
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->handle->commit();
    }
    
    /**
     * @see DbInterface::rollback()
     * {@inheritdoc}
     */
    public function rollback()
    {
        $this->handle->rollBack();
    }

    /**
     * @see DbInterface::query()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function free(object $result)
    {
        unset($result);
    }

    /**
     * @see DbInterface::lastId()
     * {@inheritdoc}
     */
    public function lastId() : int
    {
        return $this->handle->lastInsertId();
    }

    /**
     * @see DbInterface::affectedRows()
     * {@inheritdoc}
     */
    public function affectedRows() : int
    {
        return $this->result->rowCount();
    }

    /**
     * @see DbInterface::numRows()
     * {@inheritdoc}
     */
    public function numRows(object $result) : int
    {
        return $result->rowCount();
    }

    /**
     * @see DbInterface::fetchArray()
     * {@inheritdoc}
     */
    public function fetchArray(object $result) : ?array
    {
        return $result->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @see DbInterface::fetchRow()
     * {@inheritdoc}
     */
    public function fetchRow(object $result) : ?array
    {
        return $result->fetch(\PDO::FETCH_NUM) ?: null;
    }

    /**
     * @see DbInterface::fetchObject()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function fetchColumn(object $result, int $column = 0) : ?string
    {
        return $result->fetchColumn($column) ?: null;
    }

    /**
     * @see DbInterface::fetchAll()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function fetchAllFromColumn(object $result, int $column = 0) : array
    {
        return $result->fetchAll(\PDO::FETCH_COLUMN, $column);
    }
}
