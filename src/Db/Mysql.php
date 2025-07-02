<?php
/**
* The Mysql Database Driver
* @package Mars
*/

namespace Mars\Db;

/**
* The Mysql Database Driver
 */
class Mysql implements DbInterface
{
    /**
     * @var PDO $handle The PDO handle
     */
    protected ?\PDO $handle = null;

    /**
     * @var object The result of the last query operation
     */
    protected ?\PDOStatement $result;

    /**
     * @see DbInterface::connect()
     * {@inheritdoc}
     */
    public function connect(string $hostname, string $port, string $username, string $password, string $database, bool $persistent, string $charset)
    {
        $dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset={$charset}";
        $options = [\PDO::ATTR_PERSISTENT => $persistent, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
        
        try {
            $this->handle = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
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

            throw new \Exception($e->getMessage());
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
    public function free($result)
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
    public function numRows($result) : int
    {
        return $result->rowCount();
    }

    /**
     * @see DbInterface::fetchArray()
     * {@inheritdoc}
     */
    public function fetchArray($result) : array
    {
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            $row = [];
        }

        return $row;
    }

    /**
     * @see DbInterface::fetchRow()
     * {@inheritdoc}
     */
    public function fetchRow($result) : array
    {
        $row = $result->fetch(\PDO::FETCH_NUM);
        if (!$row) {
            $row = [];
        }

        return $row;
    }

    /**
     * @see DbInterface::fetchObject()
     * {@inheritdoc}
     */
    public function fetchObject($result, string $class_name = '') : ?object
    {
        if (!$class_name) {
            $class_name = '\StdClass';
        }

        $row = $result->fetchObject($class_name);
        if (!$row) {
            $row = null;
        }

        return $row;
    }
    
    /**
     * @see DbInterface::fetchColumn()
     * {@inheritdoc}
     */
    public function fetchColumn($result, int $column = 0) : ?string
    {
        $col = $result->fetchColumn($column);
        if ($col === false) {
            $col = null;
        }
        
        return $col;
    }

    /**
     * @see DbInterface::fetchAll()
     * {@inheritdoc}
     */
    public function fetchAll($result, bool|string $class_name = '') : array
    {
        if ($class_name === true) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        if (!$class_name) {
            $class_name = '\StdClass';
        }

        return $result->fetchAll(\PDO::FETCH_CLASS, $class_name);
    }
    
    /**
     * @see DbInterface::fetchAllFromColumn()
     * {@inheritdoc}
     */
    public function fetchAllFromColumn($result, int $column = 0) : array
    {
        return $result->fetchAll(\PDO::FETCH_COLUMN, $column);
    }
}
