<?php
/**
* The Database Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Db\DriverInterface;

/**
 * The Database Class
 * Handles the database interactions
 */
class Db
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

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'db', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var bool $use_multi If true, will use multiple databases for read & write queries
     */
    protected bool $use_multi {
        get => is_array($this->app->config->db_hostname);
    }

    /**
     * @var int $read_key The key of the server used for read queries
     */
    protected int $read_key {
        get {
            if (isset($this->read_key)) {
                return $this->read_key;
            }

            $this->read_key = 0;            
            if ($this->use_multi) {
                $this->read_key = mt_rand(0, count($this->app->config->db_hostname) - 1);
            }
    
            return $this->read_key;
        }
    }

    /**
     * @var string $read_hostname The hostname to connect to for read queries
     */
    protected string $read_hostname {
        get => $this->use_multi ? $this->app->config->db_hostname[$this->read_key] : $this->app->config->db_hostname;
    }

    /**
     * @var string $read_port The port to connect to for read queries
     */
    protected string $read_port {
        get => $this->use_multi ? $this->app->config->db_port[$this->read_key] : $this->app->config->db_port;
    }

    /**
     * @var string $read_username The username used to connect to the read server
     */
    protected string $read_username {
        get => $this->use_multi ? $this->app->config->db_username[$this->read_key] : $this->app->config->db_username;
    }

    /**
     * @var string $read_password The password used to connect to the read server
     */
    protected string $read_password {
        get => $this->use_multi ? $this->app->config->db_password[$this->read_key] : $this->app->config->db_password;
    }

    /**
     * @var string $read_database The database to connect to the read server
     */
    protected string $read_database {
        get => $this->use_multi ? $this->app->config->db_name[$this->read_key] : $this->app->config->db_name;
    }

    /**
     * @var bool $read_pesistent If true, the read db connection will be persistent
     */
    protected bool $read_persistent {
        get => $this->use_multi ? $this->app->config->db_persistent[$this->read_key] : $this->app->config->db_persistent;
    }

    /**
     * @var string $write_hostname The hostname to connect to for write queries
     */
    protected string $write_hostname {
        get => $this->use_multi ? $this->app->config->db_hostname[0] : $this->app->config->db_hostname;
    }

    /**
     * @var string $write_port The port to connect to for write queries
     */
    protected string $write_port {
        get => $this->use_multi ? $this->app->config->db_port[0] : $this->app->config->db_port;
    }

    /**
     * @var string $write_username The username used to connect to the write server
     */
    protected string $write_username {
        get => $this->use_multi ? $this->app->config->db_username[0] : $this->app->config->db_username;
    }

    /**
     * @var string $write_password The password used to connect to the write server
     */
    protected string $write_password {
        get => $this->use_multi ? $this->app->config->db_password[0] : $this->app->config->db_password;
    }

    /**
     * @var string $write_database The database to connect to the write server
     */
    protected string $write_database {
        get => $this->use_multi ? $this->app->config->db_name[0] : $this->app->config->db_name;
    }

    /**
     * @var bool $write_pesistent If true, the write db connection will be persistent
     */
    protected bool $write_persistent {
        get => $this->use_multi ? $this->app->config->db_persistent[0] : $this->app->config->db_persistent;
    }

    /**
     * @var DriverInterface $read_driver The handle for the read queries
     */
    public protected(set) ?DriverInterface $read_driver {
        get {
            if (isset($this->read_driver)) {
                return $this->read_driver;
            }
            if (!$this->read_database) {
                return null;
            }

            try {
                if ($this->use_multi) {
                    $this->read_driver = $this->drivers->get($this->app->config->db_driver);
                    $this->read_driver->connect($this->read_hostname, $this->read_port, $this->read_username, $this->read_password, $this->read_database, $this->read_persistent, $this->charset);                
                } else {
                    $this->read_driver = $this->write_driver;
                }
            } catch (\Exception $e) {
                throw new \Exception('Error connecting to the database: ' . $e->getMessage());
            }

            return $this->read_driver;
        }
    }

    /**
     * @var DriverInterface $write_driver The handle for the write queries
     */
    public protected(set) ?DriverInterface $write_driver {
        get {
            if (isset($this->write_driver)) {
                return $this->write_driver;
            }
            if (!$this->write_database) {
                return null;
            }

            try {
                $this->write_driver = $this->drivers->get($this->app->config->db_driver);
                $this->write_driver->connect($this->write_hostname, $this->write_port, $this->write_username, $this->write_password, $this->write_database, $this->write_persistent, $this->charset);
            } catch (\Exception $e) {
                throw new \Exception('Error connecting to the database: ' . $e->getMessage());
            }

            return $this->write_driver;
        }
    }

    /**
     * @var string $charset The charset
     */
    public string $charset {
        get => $this->app->config->db_charset;
    }
    
    /**
     * @var array $queries The list of executed queries, if debug is on
     */
    public protected(set) array $queries = [];

    /**
     * @var float $queries_time The total time needed to execute the queries, if debug is on
     */
    public protected(set) float $queries_time = 0;

    /**
     * @var bool $debug If true, the db will be run in debug mode
     */
    public bool $debug{
        get => $this->app->config->debug_db;
    }

    /**
     * @var array $last_query Contains the sql code and the params of the last executed query
     */
    protected array $last_query = ['sql' => '', 'params' => []];

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'mysql' => \Mars\Db\Mysql::class
    ];

    /**
     * Destroys the database object. Disconnects from the database server
     */
    public function __destruct()
    {
        if (isset($this->read_driver)) {
            $this->read_driver->disconnect();
        }
        if (isset($this->write_driver)) {
            $this->write_driver->disconnect();
        }
    }

    /**
     * Returns a Sql object
     * @return Sql;
     */
    public function getSql() : Sql
    {
        return new Sql($this->app);
    }

    /**
     * Begins a transaction
     */
    public function beginTransaction()
    {
        $this->write_driver->beginTransaction();
    }

    /**
     * Commits a transaction
     */
    public function commit()
    {
        $this->write_driver->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback()
    {
        $this->write_driver->rollback();
    }

    /**
     * Executes a read query
     * @param string|Sql $sql The query to execute
     * @param array $params Params to be used in prepared statements
     * @return object The result
     * @throws Exception If the query fails
     */
    public function readQuery(string|Sql $sql, array $params = []) : DbResult
    {
        return $this->query($sql, $params, true);
    }

    /**
     * Executes a write query
     * @param string|Sql $sql The query to execute
     * @param array $params Params to be used in prepared statements
     * @return object The result
     * @throws Exception If the query fails
     */
    public function writeQuery(string|Sql $sql, array $params = []) : DbResult
    {
        return $this->query($sql, $params, false);
    }

    /**
     * Executes a query
     * @param string|Sql $sql The query to execute
     * @param array $params Params to be used in prepared statements
     * @param bool $is_read If true, this is a read query
     * @return object The result
     * @throws Exception If the query fails
     */
    public function query(string|Sql $sql, array $params = [], ?bool $is_read = null) : DbResult
    {
        if ($this->debug) {
            $this->app->timer->start('sql');
        }

        if ($sql instanceof Sql) {
            $is_read = $sql->is_read;
            $params = $sql->getParams();
            $sql = $sql->sql;
        }

        $handle = $this->getQueryHandle($sql, $is_read);

        try {
            $this->last_query = ['sql' => $sql, 'params' => $params];

            $result = $handle->query($sql, $params);
        } catch (\Exception $e) {
            throw new \Exception('Query Error: ' . $this->getQueryError($e->getMessage(), $sql, $params));
        }

        if ($this->debug) {
            $exec_time = $this->app->timer->stop('sql');

            $this->queries_time+= $exec_time;
            $this->queries[] = [$this->getFullQuery($sql, $params), $exec_time];
        }

        return new DbResult($handle, $result);
    }

    /**
     * Returns the handle used to process the query
     * @param string $sql The sql query
     * @param bool $is_read If true, this is a read query
     * @return DriverInterface The handle
     */
    protected function getQueryHandle(string $sql, ?bool $is_read = null) : DriverInterface
    {
        if ($this->use_multi) {
            if ($is_read === null) {
                $is_read = $this->isReadQuery($sql);
            }

            if ($is_read) {
                return $this->read_driver;
            }

            return $this->write_driver;
        } else {
            return $this->read_driver;            
        }
    }

    /**
     * Determines if the query is a read query
     * @param string $sql The sql query
     * @return bool
     */
    protected function isReadQuery(string $sql) : bool
    {
        if (str_starts_with(trim($sql), 'select')) {
            return true;
        }

        return true;
    }

    /**
     * Returns the error message for an invalid query
     * @param string $error The error
     * @param string $sql The sql code which generated the error
     * @param array $params The query params
     * @return string The error
     */
    protected function getQueryError(string $error, string $sql, array $params)
    {
        $error = $error . "\n\n" . $sql;
        if ($params) {
            $error.= "\n\n" . print_r($params, true);
        }

        return $error;
    }

    /**
     * Returns the sql code of the last executed query.
     * !!!It should be used for debugging purposes only, as it will contain the params as well!!!
     * @return string
     */
    public function getLastQuery() : string
    {
        return $this->getFullQuery($this->last_query['sql'], $this->last_query['params']);
    }

    /**
     * Returns the full query, with the params replaced
     * !!!Is used for debugging purposes only!!!
     * @param string $sql The sql code
     * @param array $params The query params
     * @return string
     */
    protected function getFullQuery(string $sql, array $params) : string
    {
        $search = [];
        $replace = [];

        $i = 0;
        foreach ($params as $param => $value) {
            $search[$i] = ':' . $param;
            $replace[$i] = $this->read_driver->quote($value);

            $i++;
        }

        return str_replace($search, $replace, $sql);
    }

    /**
     * Selects data from a table and returns the result
     * @param string $table The table name
     * @param array $where Where conditions in the format col => val
     * @param string $order_by The order by column
     * @param string $order The order: asc/desc
     * @param int $limit The limit
     * @param int $limit_offset The limit offset, if any
     * @param string|array $cols The columns to select
     * @return DbResult The result
     */
    public function select(string $table, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string|array $cols = '*') : array
    {
        $sql = $this->getSql()->select($cols)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

        return $this->query($sql)->fetchAll();
    }

    /**
     * Selects a single row from the database
     * @param string $table The table name
     * @param array $where Where conditions in the format col => val
     * @param string|array $cols The columns to select
     * @return object The row
     */
    public function selectRow(string $table, array $where = [], string|array $cols = '*') : ?object
    {
        $sql = $this->getSql()->select($cols)->from($table)->where($where)->limit(1);

        return $this->query($sql)->fetchObject();
    }

    /**
     * Selects a single row from the database, by id
     * @param string $table The table name
     * @param int $id The id of the row to return
     * @param string $id_col The name of the id column
     * @param string|array $cols The columns to select
     * @return object The row
     */
    public function selectById(string $table, int $id, string $id_col = 'id', string|array $cols = '*') : ?object
    {
        $sql = $this->getSql()->select($cols)->from($table)->where([$id_col => $id])->limit(1);

        return $this->query($sql)->fetchObject();
    }

    /**
     * Selects multiple rows from the database, by id
     * @param string $table The table name
     * @param array $ids Array with the ids for which we're retriving data
     * @param string $order_by The order by column
     * @param string $order The order: asc/desc
     * @param string $id_col The name of the id column
     * @param string|array $cols The columns to select
     * @return array Returns the rows as an array with the id as a key
     */
    public function selectByIds(string $table, array $ids, string $order_by = '', string $order = '', string $id_col = 'id', string|array $cols = '*') : array
    {
        if (!$ids) {
            return [];
        }

        $sql = $this->getSql()->select($cols)->from($table)->whereIn($id_col, $ids)->orderBy($order_by, $order);

        return $this->query($sql)->get($id_col);
    }

    /**
     * Selects a single column and returns the result
     * @param string $col The column to select
     * @see Db::select()
     * @return array The IDs
     */
    public function selectCol(string $table, string $col, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
    {
        $sql = $this->getSql()->select($col)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

        return $this->query($sql)->getCol();
    }

    /**
     * Returns all the Ids from a table
     * @param string $col The id col
     * @see Db::select()
     * @return array The IDs
     */
    public function selectIds(string $table, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $col = 'id') : array
    {
        return $this->selectCol($table, $col, $where, $order_by, $order, $limit, $limit_offset);
    }

    /**
     * Returns a key=>value pair with values from two columns
     * @param string $key_col The name of the column used as the key
     * @param string $col The name of the column used as the value
     * @see Db::select()
     * @return array
     */
    public function selectList(string $table, string $key_col, string $col, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
    {
        $sql = $this->getSql()->select([$key_col, $col])->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

        return $this->query($sql)->get($key_col, $col);
    }

    /**
     * Returns the first column from the first row generated by a query
     * @param string $table The table name
     * @param string $col The column to return
     * @param array $where Where conditions in the format col => val
     * @return string The result
     */
    public function selectResult(string $table, string $col, array $where = []) : ?string
    {
        $sql = $this->getSql()->select([$col])->from($table)->where($where)->limit(1);

        return $this->query($sql)->getResult();
    }

    /**
     * Determines if a row matching some conditions exists
     * @param string $table The table name
     * @param array $where Where conditions in the format col => val
     * @param string $col The column to include in the FROM clause. Should be the primary key, for performance purposes, if possible
     * @return bool True if the row exists, false otherwise
     */
    public function exists(string $table, array $where, string $col = '') : bool
    {
        if (!$col) {
            if ($where) {
                $col = key($where);
            } else {
                $col = '*';
            }
        }

        $sql = $this->getSql()->select([$col])->from($table)->where($where)->limit(1);

        return (bool)$this->query($sql)->numRows();
    }

    /**
     * Builds a count query. The format is: select count(*) from $table $where
     * @param string $table The table name
     * @param array $where Where conditions in the format col => val
     * @return int The number of rows
     */
    public function count(string $table, array $where = []) : int
    {
        $sql = $this->getSql()->select('COUNT(*)')->from($table)->where($where);

        return $this->query($sql)->getCount();
    }

    /**
     * Counts the rows with the ids defined in $ids
     * @param string $table The table name
     * @param array $ids Array with the ids for which we're retriving the count
     * @param string $id_col The name of the id column
     * @return int The number of rows
     */
    public function countById(string $table, array $ids, string $id_col = 'id') : int
    {
        if (!$ids) {
            return 0;
        }

        $sql = $this->getSql()->select('COUNT(*)')->from($table)->whereIn($id_col, $ids);

        return $this->query($sql)->getCount();
    }

    /**
     * Inserts data into a table
     * @param string $table The table
     * @param array $values The data to insert in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return int Returns the id of the newly inserted row
     */
    public function insert(string $table, array $values) : int
    {
        if (!$values) {
            return 0;
        }

        $sql = $this->getSql()->insert($table)->values($values);

        return $this->query($sql)->lastId();
    }

    /**
     * Does a multiple insert
     * @param string $table The table
     * @param array $values_list Array containing the list of data to insert. Eg: [ ['foo' => 'bar'], ['foo' => 'bar2'] ... ]
     * @return int The number of inserted rows
     */
    public function insertMulti(string $table, array $values_list) : int
    {
        if (!$values_list) {
            return 0;
        }

        $sql = $this->getSql()->insert($table)->valuesMulti($values_list);

        return $this->query($sql)->affectedRows();
    }

    /**
     * Updates data
     * @param string $table The table
     * @param array $values The data to updated in the column => value format. If value is an array it will be updated as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @param array $where Where conditions in the format col => val
     * @param int $limit The limit, if any
     * @return int The number of affected rows
     */
    public function update(string $table, array $values, array $where = [], int $limit = 0) : int
    {
        if (!$values) {
            return 0;
        }

        $sql = $this->getSql()->update($table)->set($values)->where($where)->limit($limit);

        return $this->query($sql)->affectedRows();
    }

    /**
     * Updates a single row in the database based on id
     * @param $table The table
     * @param array $values The data to be updated. @see Db::update()
     * @param int $id The id of the row to be updated
     * @param string $id_col The name of the id column
     * @return int The number of affected rows
     */
    public function updateById(string $table, array $values, int $id, string $id_col = 'id') : int
    {
        return $this->update($table, $values, [$id_col => $id], 1);
    }

    /**
     * Updates multiple rows in the database based on id
     * @param string $table The table to update
     * @param array $values @see update
     * @param array $ids Array with the ids of the rows to update
     * @param string $id_col The name of the id column
     * @return int The number of affected rows
     */
    public function updateByIds(string $table, array $values, array $ids, string $id_col = 'id') : int
    {
        if (!$values || !$ids) {
            return 0;
        }

        $sql = $this->getSql()->update($table)->set($values)->whereIn($id_col, $ids);

        return $this->query($sql)->affectedRows();
    }

    /**
     * Executes a replace query
     * @param string $table The table
     * @param array $values The data to update in the column => value format. If value is an array it will be inserted without quotes/escaping. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return int Returns the id of the inserted row
     */
    public function replace(string $table, array $values) : int
    {
        if (!$values) {
            return 0;
        }

        $sql = $this->getSql()->replace($table)->set($values);

        return $this->query($sql)->lastId();
    }

    /**
     * Deletes rows from a table
     * @param string $table The table
     * @param array $where Where conditions in the format col => val
     * @param int $limit The limit, if any
     * @return int The number of affected rows
     */
    public function delete(string $table, array $where = [], int $limit = 0) : int
    {
        $sql = $this->getSql()->delete()->from($table)->where($where)->limit($limit);

        return $this->query($sql)->affectedRows();
    }

    /**
     * Deletes a single row in the database based on id
     * @param $table The table
     * @param int $id The id of the row to be deleted
     * @param string $id_col The name of the id column
     * @return int The number of affected rows
     */
    public function deleteById(string $table, int $id, string $id_col = 'id') : int
    {
        return $this->delete($table, [$id_col => $id], 1);
    }

    /**
     * Deletes multiple rows in the database based on id
     * @param $table The table
     * @param array $ids Array with the ids of the rows to update
     * @param string $id_col The name of the id column
     * @return int The number of affected rows
     */
    public function deleteByIds(string $table, array $ids, string $id_col = 'id') : int
    {
        if (!$ids) {
            return 0;
        }

        $sql = $this->getSql()->delete()->from($table)->whereIn($id_col, $ids);

        return $this->query($sql)->affectedRows();
    }

    /**
     * Returns the NOW function
     * @return array
     */
    public function now() : array
    {
        return ['function' => 'NOW'];
    }

    /**
     * Returns the UNIX_TIMESTAMP function
     * @return array
     */
    public function unixTimestamp() : array
    {
        return ['function' => 'UNIX_TIMESTAMP'];
    }

    /**
     * Returns the CRC32 function
     * @param string $value The value for which to compute the crc
     * @return array
     */
    public function crc32(string $value) : array
    {
        return ['function' => 'CRC32', 'value' => $value];
    }

    /**
     * Returns the columns of table $table and the type
     * @param string $table The name of the table
     * @param bool $primary_key If false, will not return the primary key
     * @param bool $cache If true, will cache the result
     * @return array Returns the table columns in the format name => type
     */
    public function getColumns(string $table, bool $primary_key = true, bool $cache = true) : array
    {
        static $columns_cache = [];
        $key = $table . ($primary_key ? '1' : '0');

        if ($cache && isset($columns_cache[$key])) {
            return $columns_cache[$key];
        }

        $cols = [];
        $columns = $this->readQuery("SHOW COLUMNS FROM {$table}")->fetchAll();
        foreach ($columns as $col) {
            $name = $col->Field;
            $type = $col->Type;
            $key = $col->Key;

            if (!$primary_key && $key == 'PRI') {
                continue;
            }

            if (str_contains($type, 'int')) {
                $type = 'int';
            } elseif (str_contains($type, 'float')) {
                $type = 'float';
            } else {
                $type = 'string';
            }

            $cols[$name] = $type;
        }

        if ($cache) {
            $columns_cache[$key] = $cols;
        }

        return $cols;
    }

    /**
     * Builds a set from table $table with values from $values. The values are filtered based on the column's type
     * The difference between bind and bindList is bind is blacklisting the columns which shouldn't be included; bindList is whitelisting the desired columns.
     * @param string $table The name of the table
     * @param array $values The values in the col => value format
     * @param array $ignore_columns Array listing the columns from $table which shouldn't be included in the returned result
     * @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
     * @param string $values_prefix Prefix to be used on $values, if any
     * @return array The data
     */
    public function bind(string $table, array $values = [], array $ignore_columns = [], ?string $ignore_value = null, string $values_prefix = '') : array
    {
        $data = $this->getBindData($table, $values, $ignore_value, $values_prefix);

        return array_filter($data, function ($col) use ($ignore_columns) {
            return !in_array($col, $ignore_columns);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Builds a set from table $table with values from $data. The values are filtered based on the column's type
     * The difference between bind and bindList is bind is blacklisting the columns which shouldn't be included; bindList is whitelisting the desired columns.
     * @param string $table The name of the table
     * @param array $values The values in the col => value format
     * @param array $allowed_columns Array listing the columns of table $table for which the request values should be bind
     * @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
     * @param string $values_prefix Prefix to be used on $values, if any
     * @return array
     */
    public function bindList(string $table, array $values = [], array $allowed_columns = [], ?string $ignore_value = null, string $values_prefix = '') : array
    {
        $data = $this->getBindData($table, $values, $ignore_value, $values_prefix);

        return array_filter($data, function ($col) use ($allowed_columns) {
            return in_array($col, $allowed_columns);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Returns the data used by the bind/bindList operations
     * @see Db::bind()
     */
    protected function getBindData(string $table, array $values, ?string $ignore_value, string $values_prefix) : array
    {
        $data = [];
        $columns = $this->getColumns($table);

        foreach ($columns as $name => $type) {
            $key = $values_prefix . $name;

            if (!isset($values[$key])) {
                continue;
            }

            $value = $this->app->filter->value($values[$key], $type);

            if ($ignore_value !==null && $value == $ignore_value) {
                continue;
            }

            $data[$name] = $value;
        }

        return $data;
    }

    /**
     * Returns an array from $table with the columns filled based on their type [int,float,string]
     * @param string $table The name of the table
     * @param array $override_array If specified will override the default filling
     * @param array $ignore_array Columns to exclude from the list, if any
     * @param int $int_val The value used to fill int/float columns
     * @param string $string_val The value used to fill char/string columns
     * @param bool $primary_key If true will also return the primary array
     * @return array
     */
    public function fill(string $table, array $override_array = [], array $ignore_array = [], int $int_val = 0, string $string_val = '', bool $primary_key = false) : array
    {
        $data = [];
        $columns = $this->getColumns($table, $primary_key);

        foreach ($columns as $name => $type) {
            $value = '';
            switch ($type) {
                case 'int':
                case 'float':
                    $value = $int_val;
                    break;
                default:
                    $value = $string_val;
            }

            $data[$name] = $value;
        }

        if ($override_array) {
            $data = array_merge($data, $override_array);
        }

        if ($ignore_array) {
            $data = App::filterProperties($data, $ignore_array);
        }

        return $data;
    }
}
