<?php
/**
* The SQL Driver Interface
* @package Mars
*/

namespace Mars\Db\Sql\Drivers;

/**
 * The SQL Driver Interface
 */
interface SqlInterface
{
    /**
     * Starts the query
     */
    public function start();

    /**
     * Builds an INSERT query
     * @param string $table The table to insert into
     * @return string
     */
    public function insert(string $table) : string;

    /**
     * Builds an UPDATE query
     * @param string $table The table
     * @return string
     */
    public function update(string $table) : string;

    /**
     * Builds a REPLACE query
     * @param string $table The table
     * @return string
     */
    public function replace(string $table) : string;

    /**
     * Builds a DELETE query
     * @return string
     */
    public function delete() : string;

    /**
     * Builds the VALUES part of an INSERT query
     * @param array $values The data to insert in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return string
     */
    public function values(array $values) : string;

    /**
     * Builds the VALUES part of an INSERT query for multiple rows
     * @param array $values_list The data to insert in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return string
     */
    public function valuesMulti(array $values_list) : string;

    /**
     * Builds the SET part of an UPDATE query
     * @param array $values The data to update in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
     * @return string
     */
    public function set(array $values) : string;

    /**
     * Builds a SELECT query.
     * @param string|array $cols The columns to select.
     * @param string|array $extra Extra options.
     * @return string
     */
    public function select(string|array $cols = '*', string|array $extra = '') : string;

    /**
     * Builds a SELECT COUNT query.
     * @return string
     */
    public function selectCount() : string;

    /**
     * Adds the FROM clause.
     * @param string $table The table.
     * @param string $alias The alias of the table, if any.
     * @return string
     */
    public function from(string $table) : string;

    /**
     * Adds a JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom SQL to add in the ON part of the join clause, if $using is empty
     * @return string
     */
    public function join(string $table, string $using = '', string $on = '') : string;

    /**
     * Adds a LEFT JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom SQL to add in the ON part of the join clause, if $using is empty
     * @return string
     */
    public function leftJoin(string $table, string $using = '', string $on = '') : string;

    /**
     * Adds a RIGHT JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom SQL to add in the ON part of the join clause, if $using is empty
     * @return string
     */
    public function rightJoin(string $table, string $using = '', string $on = '') : string;

    /**
     * Adds a INNER JOIN clause
     * @param string $table The table to join
     * @param string $alias The alias of the table, if any
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom SQL to add in the ON part of the join clause, if $using is empty
     * @return string
     */
    public function innerJoin(string $table, string $using = '', string $on = '') : string;

    /**
     * Adds a WHERE clause
     * @param array $where The where conditions. The format must be: column => value or column => [p1, p2, p3] or column => ['operator' => '>', 'value' => 10, 'function' => UNIX_TIMESTAMP]  
     * @param string $delimitator The delimitator to use between the conditions
     * @return string
     */
    public function where(array $where, string $delimitator = 'AND') : string;

    /**
     * Returns a WHERE IN(...) clause
     * @param string $column The column
     * @param array $values Array with the elements to place in the IN list. The values are considered as ints
     * @return string
     */
    public function whereIn(string $column, array $values) : string;

    /**
     * Returns the AND keyword
     * @return string
     */
    public function and() : string;

    /**
     * Returns the OR keyword
     * @return string
     */
    public function or() : string;

    /**
     * Builds a HAVING clause
     * @param array $having The having conditions. The format must be: function => value or function => ['operator' => '>', 'value' => 10]
     * @param string $delimitator The delimitator to use between parts. By default AND is used.
     * @return string
     */
    public function having(array $having, string $delimitator = 'AND') : string;

    /**
     * Returns an ORDER BY clause
     * @param string $order_by The order by column
     * @param string $order The order: asc/desc
     * @return string
     */
    public function orderBy(string $order_by, string $order = '') : string;

    /**
     * Returns a GROUP BY clause
     * @param string $group_by The group by column
     * @return string
     */
    public function groupBy(string $group_by) : string;

    /**
     * Returns a LIMIT clause
     * @param int $count The number of items
     * @param int int The offset, if any
     */
    public function limit(int $count, ?int $offset = null) : string;

    /**
     * Returns an OFFSET clause
     * @param int $offset The offset
     * @return string
     */
    public function offset(int $offset) : string;
}