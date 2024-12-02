<?php
/**
* The Sql Builder Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;

use Mars\Sql\DriverInterface;

/**
 * The Sql Builder Class.
 * Builds sql code
 */
class Sql implements \Stringable
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
     * @var DriverInterface $driver The driver object
     */
    protected DriverInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->db_driver);

            return $this->driver;
        }
    }

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'mysql' => \Mars\Sql\Mysql::class
    ];

    /**
     * @var string $sql The sql code
     */
    public protected(set) string $sql = '';    

    /**
     * @var bool $is_read Determines if the statement is a read statement
     */
    public protected(set) bool $is_read = false;

    /**
     * Converts the sql to a string
     */
    public function __toString()
    {
        return $this->sql;
    }

    /**
     * Runs the SQL code as a query
     * @param return DbResult
     */
    public function query() : DbResult
    {
        return $this->app->db->query($this, $this->driver->params);
    }

    /**
     * Returns the sql code
     * @return string
     */
    public function getSql() : string
    {
        return $this->sql;
    }

    /**
     * Returns the params
     * @return array
     */
    public function getParams() : array
    {
        return $this->driver->params;
    }

    /**
     * Starts a new statement
     * @param bool $is_read The tpye of the statement
     * @return static
     */
    protected function start(bool $is_read = false)
    {
        $this->sql = '';
        $this->is_read = $is_read;    

        $this->driver->start();

        return $this;
    }

    /**
     * Builds an INSERT query
     * @param string $table The table to insert into
     * @return static
     */
    public function insert(string $table) : static
    {
        $this->start();

        $this->sql = $this->driver->insert($table);

        return $this;
    }

    /**
     * Builds an UPDATE query
     * @param string $table The table
     * @return static
     */
    public function update(string $table) : static
    {
        $this->start();

        $this->sql = $this->driver->update($table);

        return $this;
    }

    /**
     * Builds a REPLACE query
     * @param string $table The table
     * @return static
     */
    public function replace(string $table) : static
    {
        $this->start();

        $this->sql = $this->driver->replace($table);

        return $this;
    }

    /**
     * Builds a DELETE query
     * @return static
     */
    public function delete() : static
    {
        $this->start();

        $this->sql = $this->driver->delete();

        return $this;
    }

    /**
     * Builds the VALUES part of an INSERT query
     * @param array $values The data to insert in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return static
     */
    public function values(array $values) : static
    {       
        $this->sql.= $this->driver->values($values);

        return $this;
    }

    /**
     * Builds the VALUES part of an INSERT query by generating multiple values
     * @param array $values_list Array containing the list of data to insert. Eg: [ ['foo' => 'bar'], ['foo' => 'bar2'] ... ]
     * @return static
     */
    public function valuesMulti(array $values_list) : static
    {        
        $this->sql.= $this->driver->valuesMulti($values_list);

        return $this;
    }

    /**
     * Builds the SET part of an update query
     * @param array $values The data to updated in the column => value format. If value is an array it will be updated as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return static
     */
    public function set(array $values) : static
    {
        $this->sql.= $this->driver->set($values);

        return $this;
    }

    /**
     * Builds a SELECT query
     * @param string|array $cols The cols to select
     * @return static
     */
    public function select(string|array $cols = '*', string|array $extra = '') : static
    {
        $this->start(true);
        
        $this->sql = $this->driver->select($cols, $extra);

        return $this;
    }

    /**
     * Builds a SELECT COUNT(*) query
     * @return static
     */
    public function selectCount() : static
    {
        $this->start(true);
        
        $this->sql = $this->driver->selectCount();

        return $this;
    }

    /**
     * Adds the FROM clause
     * @param string $table The table
     * @param string $alias The alias of the table, if any
     * @return static
     */
    public function from(string $table, string $alias = '') : static
    {        
        $this->sql.= $this->driver->from($table, $alias);

        return $this;
    }

    /**
     * Adds a JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
     * @return static
     */
    public function join(string $table, string $alias = '', string $using = '', string $on = '') : static
    {        
        $this->sql.= $this->driver->join($table, $alias, $using, $on);

        return $this;
    }

    /**
     * Adds a LEFT JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
     * @return static
     */
    public function leftJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
    {
        $this->sql.= $this->driver->leftJoin($table, $alias, $using, $on) . ' ';

        return $this;
    }

    /**
     * Adds a RIGHT JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
     * @return static
     */
    public function rightJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
    {
        $table = $this->escapeTable($table, $alias);

        $this->sql.= " RIGHT JOIN {$table}" . $this->getJoinSql($using, $on);

        return $this;
    }

    /**
     * Adds a INNER JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
     * @return static
     */
    public function innerJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
    {
        $table = $this->escapeTable($table, $alias);

        $this->sql.= " INNER JOIN {$table}" . $this->getJoinSql($using, $on);

        return $this;
    }    

    /**
     * Builds a WHERE clause
     * @param array $where The where conditions. The format must be: column => value or column => [p1, p2, p3] or column => ['operator' => '>', 'value' => 10, 'function' => UNIX_TIMESTAMP]  
     * @param string $delimitator The delimitator to use between parts. By default AND is used.
     * @return static
     */
    public function where(array $where, string $delimitator = 'AND') : static
    {    
        $this->sql.= $this->driver->where($where, $delimitator);

        return $this;
    }

    /**
     * Returns a WHERE IN(...) clause
     * @param string $column The column
     * @param array $values Array with the elements to place in the IN list
     * @param bool $is_int If true,will treat the elements from $in_array as int values
     * @return static
     */
    public function whereIn(string $column, array $values) : static
    {
        if (!$values) {
            return $this;
        }

        $values = $this->app->filter->int($values);

        $this->sql.=  $this->driver->whereIn($column, $values);

        return $this;
    }

    /**
     * Returns the AND keyword
     * @return static
     */
    public function and() : static
    {
        $this->sql.= $this->driver->and();

        return $this;
    }

    /**
     * Returns the AND keyword
     * @return static
     */
    public function or() : static
    {
        $this->sql.= $this->driver->or();

        return $this;
    }

    /**
     * Builds a HAVING clause
     * @param array $having The having conditions. The format must be: function => value or function => ['operator' => '>', 'value' => 10]
     * @param string $delimitator The delimitator to use between parts. By default AND is used.
     * @return static
     */
    public function having(array $having, string $delimitator = 'AND') : static
    {       
        $this->sql.= $this->driver->having($having, $delimitator);

        return $this;
    }

    /**
     * Returns an ORDER BY clause
     * @param string $order_by The order by column
     * @param string $order The order: asc/desc
     * @return static
     */
    public function orderBy(string $order_by, string $order = '') : static
    {
        $this->sql.= $this->driver->orderBy($order_by, $order);

        return $this;
    }

    /**
     * Returns a GROUP BY clause
     * @param string $group_by The group by column
     * @return static
     */
    public function groupBy(string $group_by) : static
    {
        $this->sql.= $this->driver->groupBy($group_by);

        return $this;
    }

    /**
     * Returns a LIMIT clause
     * @param int $count The number of items
     * @param int int The offset, if any
     * @return static
     */
    public function limit(int $count, ?int $offset = null) : static
    {
        $this->sql.= $this->driver->limit($count, $offset);

        return $this;
    }

    /**
     * Returns an OFFSET clause
     * @param int $offset The offset
     * @return static
     */
    public function offset(int $offset) : static
    {
        $this->sql.= $this->driver->offset($offset);

        return $this;
    }

    /**
     * Returns a LIMIT clause corresponding to the current page
     * @param int $page The page number of the current page
     * @param int $page_items Items per page
     * @param int $total_items The total number of items.
     * @return static
     */
    public function pageLimit(int $page = 0, int $page_items = 0, int $total_items = 0) : static
    {
        $page--;

        if ($page < 0) {
            $page = 1;
        }

        if ($total_items) {
            $nr_pages = ceil($total_items / $page_items);
            if ($page >= $nr_pages) {
                $page = 1;
            }
        }

        $offset = $page * $page_items;

        $this->limit($page_items, $offset);

        return $this;
    }
}
