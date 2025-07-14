<?php
/**
* The Sql Builder Base Class.
* @package Mars
*/

namespace Mars\Db\Sql\Drivers;

/**
 * The Sql Builder Base Class.
 */
abstract class Sql implements SqlInterface
{
    /**
     * @var array $params The params to use in prepared statements
     */
    public protected(set) array $params = [];

    /**
     * @internal
     */
    protected int $param_index = 0;

    /**
     * @internal
     */
    protected int $in_index = 0;

    /**
     * @internal
     */
    protected bool $where = false;

    /**
     * @internal
     */
    protected bool $having = false;

    /**
     * @see SqlInterface::start()
     * {@inheritdoc}
     */
    public function start()
    {
        $this->params = [];
        $this->param_index = 0;
        $this->in_index = 0;
        $this->where = false;
        $this->having = false;
    }

    /**
     * Adds a param to the params list
     * @param string $param The param
     * @param string $value The value of the param
     * @return string The param, prepanded by ':'
     */
    protected function addParam(string $param, string $value) : string
    {
        if (isset($this->params[$param])) {
            $param = $param . '_' . mt_rand(0, 9999999999);
        }

        $this->params[$param] = $value;

        return ':' . $param;
    }

    /**
     * Generates a param name
     */
    protected function generateParam() : string
    {
        $param = 'param_' . $this->param_index;
        $this->param_index++;

        return $param;
    }

    /**
     * Escapes a table name
     * @param string $table The table
     * @param string $alias The alias of the table, if any
     */
    protected function escapeTable(string $table, string $alias = '') : string
    {
        $as = ' AS ';
        $table = str_ireplace(' as ', ' AS ', $table);
        $parts = explode($as, $table);
        if (count($parts) == 1) {
            $parts = explode(' ', $table);
            $as = ' ';
        }

        $db = '';
        $table = $parts[0];
        $alias = $parts[1] ?? '';

        if (str_contains($table, '.')) {
            [$db, $table] = explode('.', $table);
        }

        $table = trim($table, " '`");

        if ($db) {
            $db.= '.';
        }
        if ($alias) {
            $alias = $as . trim($alias);
        }

        return "{$db}`{$table}`{$alias}";
    }

    /**
     * Escapes a column name
     * @param string $column The column to escape
     * @return string The escaped column name
     */
    protected function escapeColumn(string $column) : string
    {
        return '`' . $column . '`';
    }

    /**
     * Escapes a value meant to be used in a like %% part
     * @param string $value The value to escape
     * @return string The escaped value
     */
    protected function escapeLike(string $value) : string
    {
        return str_replace('%', '\%', $value);
    }

    /**
     * @see SqlInterface::insert()
     * {@inheritdoc}
     */
    public function insert(string $table) :string
    {
        return 'INSERT INTO ' . $this->escapeTable($table) . ' ';
    }

    /**
     * @see SqlInterface::update()
     * {@inheritdoc}
     */
    public function update(string $table) : string
    {
        return 'UPDATE ' . $this->escapeTable($table) . ' ';
    }

    /**
     * @see SqlInterface::replace()
     * {@inheritdoc}
     */
    public function replace(string $table) : string
    {
        return 'REPLACE INTO ' . $this->escapeTable($table) . ' ';
    }

    /**
     * @see SqlInterface::delete()
     * {@inheritdoc}
     */
    public function delete() : string
    {
        return 'DELETE ';
    }

    /**
     * @see SqlInterface::values()
     * {@inheritdoc}
     */
    public function values(array $values) : string
    {
        $cols = $this->getColumnsList(array_keys($values));
        $values = $this->getValuesList($values);

        return "({$cols}) VALUES ({$values}) ";
    }

    /**
     * @see SqlInterface::valuesMulti()
     * {@inheritdoc}
     */
    public function valuesMulti(array $values_list) : string
    {
        $cols = $this->getColumnsList(array_keys(reset($values_list)));

        $list = [];
        foreach ($values_list as $key => $values) {
            $list[] = '(' . $this->getValuesList($values, $key) . ')';
        }

        return "({$cols}) VALUES " . implode(', ', $list) . ' ';
    }

    /**
     * Returns the values of an INSERT query
     * @param array $values The values to insert
     * @param string $suffix Suffix, if any, to add to the name of params
     * @return string The values
     */
    protected function getValuesList(array $values, string $suffix = '') : string
    {
        $vals = [];
        foreach ($values as $col => $value) {
            if ($suffix !== '') {
                $col = $col . '_' . $suffix;
            }

            if (is_array($value)) {
                $vals[] = $this->getValue($col, $value);
            } else {
                $vals[] = $this->addParam($col, $value);
            }
        }

        return implode(', ', $vals);
    }

    /**
     * Returns the value to be inserted/updated from an array
     * @param string $col The column
     * @param string $value The value. Can contain the function/value keys
     * @return string The value
     */
    protected function getValue(string $col, array $value) : string
    {
        //if there is a 'function' key specified, use it to return the value as a SQL function
        if (isset($value['function'])) {
            $func = strtoupper($value['function']);

            if (isset($value['value'])) {
                return $func . '(' . $this->addParam($col, $value['value']) . ')';
            } else {
                return $func . '()';
            }
        } else {
            if (isset($value['value'])) {
                return $this->addParam($col, $value['value']);
            } else {
                return reset($value);
            }
        }
    }

    /**
     * @see SqlInterface::set()
     * {@inheritdoc}
     */
    public function set(array $values) : string
    {
        return 'SET ' . $this->getSetList($values) . ' ';
    }

    /**
     * Returns the fields of an SET part
     * @param array $values The values to insert
     * @return string The fields
     */
    protected function getSetList(array $values) : string
    {
        $vals = [];
        foreach ($values as $col => $value) {
            $col_esc = $this->escapeColumn($col);

            if (is_array($value)) {
                $vals[] = $col_esc . ' = ' . $this->getValue($col, $value);
            } else {
                $vals[] = $col_esc . ' = ' . $this->addParam($col, $value);
            }
        }

        return implode(', ', $vals);
    }

    /**
     * @see SqlInterface::select()
     * {@inheritdoc}
     */
    public function select(string|array $cols = '*', string|array $extra = '') : string
    {
        if (is_array($cols)) {
            $cols = $this->getColumnsList($cols);
        }
        if ($extra) {
            if (is_array($extra)) {
                $extra = implode(' ', $extra);
            }
            $extra.= ' ';
        }

        return "SELECT {$extra}{$cols} ";
    }

    /**
     * @see SqlInterface::selectCount()
     * {@inheritdoc}
     */
    public function selectCount() : string
    {
        return "SELECT COUNT(*) ";
    }

    /**
     * Returns a list of columns, delimited by comma
     * @param array $cols The columns
     * @return string The column list
     */
    protected function getColumnsList(array $cols): string
    {
        array_walk($cols, function (&$col) {
            $col = $this->escapeColumn($col);
        });

        return implode(', ', $cols);
    }

    /**
     * @see SqlInterface::from()
     * {@inheritdoc}
     */
    public function from(string $table) : string
    {
        return 'FROM ' . $this->escapeTable($table) . ' ';
    }

    /**
     * @see SqlInterface::join()
     * {@inheritdoc}
     */
    public function join(string $table, string $using = '', string $on = '') : string
    {
        return $this->getJoin('JOIN', $table, $using, $on);
    }

    /**
     * @see SqlInterface::leftJoin()
     * {@inheritdoc}
     */
    public function leftJoin(string $table, string $using = '', string $on = '') : string
    {
        return $this->getJoin('LEFT JOIN', $table, $using, $on);
    }

    /**
     * @see SqlInterface::rightJoin()
     * {@inheritdoc}
     */
    public function rightJoin(string $table, string $using = '', string $on = '') : string
    {
        return $this->getJoin('RIGHT JOIN', $table, $using, $on);
    }

    /**
     * @see SqlInterface::innerJoin()
     * {@inheritdoc}
     */
    public function innerJoin(string $table, string $using = '', string $on = '') : string
    {
        return $this->getJoin('INNER JOIN', $table, $using, $on);
    }

    /**
     * {@internal}
     */
    protected function getJoin(string $join, string $table, string $using = '', string $on = '') : string
    {
        $table = $this->escapeTable($table);

        return "{$join} {$table}" . $this->getJoinSql($using, $on) . ' ';
    }

    /**
     * Builds the USING or OR part of a join
     * @param string $using The column used in the USING part, if any
     * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
     */
    protected function getJoinSql(string $using, string $on) : string
    {
        if ($using) {
            return ' USING (' . $this->escapeColumn($using) . ')';
        } elseif ($on) {
            return " ON {$on}";
        }

        return '';
    }

    /**
     * @see SqlInterface::where()
     * {@inheritdoc}
     */
    public function where(array $where, string $delimitator = 'AND') : string
    {
        if (!$where) {
            return '';
        }

        return $this->getWhere() . '(' . $this->getConditions($where, $delimitator) . ') ';
    }

    /**
     * Starts the WHERE clause
     * @return string
     */
    protected function getWhere() : string
    {
        if ($this->where) {
            return ' ';
        }

        $this->where = true;

        return 'WHERE ';
    }

    /**
     * @see SqlInterface::whereIn()
     * {@inheritdoc}
     */
    public function whereIn(string $column, array $values) : string
    {
        if (!$values) {
            return $this;
        }

        return $this->getWhere() . $this->escapeColumn($column) . $this->getIn($values, true);
    }

    /**
     * @see SqlInterface::and()
     * {@inheritdoc}
     */
    public function and() : string
    {
        return ' AND ';
    }

    /**
     * @see SqlInterface::or()
     * {@inheritdoc}
     */
    public function or() : string
    {
        return ' OR ';
    }

    /**
     * @see SqlInterface::having()
     * {@inheritdoc}
     */
    public function having(array $having, string $delimitator = 'AND') : string
    {
        if (!$having) {
            return '';
        }

        return $this->getHaving() . '(' . $this->getConditions($having, $delimitator, false, true) . ') ';
    }

    /**
     * Starts the HAVING clause
     * @return string
     */
    protected function getHaving() : string
    {
        if ($this->having) {
            return ' ';
        }

        $this->having = true;

        return 'HAVING ';
    }
    
    /**
     * Builds multiple conditions
     * @param array $conditions The conditions
     * @param string $delimitator The delimitator to use
     * @param bool If true, will escape the column
     * @param bool If true, will generate param names
     * @return string
     */
    protected function getConditions(array $conditions, string $delimitator, bool $escape_col = true, bool $generate_param = false) : string
    {
        $parts = [];
        foreach ($conditions as $col => $value) {
            $col_esc = $escape_col ? $this->escapeColumn($col) : $col;
            $col = $generate_param ? $this->generateParam() : $col;

            if (is_array($value)) {
                if (array_is_list($value)) {
                    //if the value is an array, it's an IN list
                    $parts[] = $col_esc . $this->getIn($value);
                } else {
                    //the array contains the operator and/or the value and/or the function to apply
                    $parts[] = $col_esc . ' ' . $this->prepareValue($col, $value);
                }
            } else {
                $parts[] = $col_esc . ' = ' . $this->addParam($col, $value);
            }
        }

        return implode(' ' . $delimitator . ' ', $parts);
    }

    /**
     * Returns an IN(...) list
     * @param array $values The IN values
     * @return string
     */
    protected function getIn(array $values, bool $is_raw = false) : string
    {
        if (!$is_raw) {
            $key = 0;
            foreach ($values as $value) {
                //generate a param for each IN value
                $col = 'in_' . $this->in_index . '_' . $key;
                $values[$key] = $this->addParam($col, $value);

                $key++;
            }

            $this->in_index++;
        }

        return ' IN(' . implode(', ', $values) . ') ';
    }

    /**
     * Returns the operator - value SQL part
     * @param string $col The column
     * @param array $value The value
     * @return string
     */
    protected function prepareValue(string $col, array $value) : string
    {
        $operator = $value['operator'] ?? '=';
        $string_value = $value['value'] ?? '';

        switch (strtolower($operator)) {
            case 'like':
                $value = '%' . $this->escapeLike($string_value) . '%';
                return 'LIKE ' . $this->addParam($col, $value) . ' ';
            case 'like_simple':
                $value = $this->escapeLike($string_value);
                return 'LIKE ' . $this->addParam($col, $value) . ' ';
                break;
            case 'like_left':
                $value = '%' . $this->escapeLike($string_value);
                return 'LIKE ' . $this->addParam($col, $value) . ' ';
                break;
            case 'like_right':
                $value = $this->escapeLike($string_value) . '%';
                return 'LIKE ' . $this->addParam($col, $value) . ' ';
                break;
            default:
                return $operator . ' ' . $this->getValue($col, $value);
        }

        return $value;
    }

    /**
     * @see SqlInterface::orderBy()
     * {@inheritdoc}
     */
    public function orderBy(string $order_by, string $order = '') : string
    {
        if (!$order_by) {
            return '';
        }

        $order_by = $this->escapeColumn($order_by);
        $order = strtoupper(trim($order));

        if ($order == 'ASC' || $order == 'DESC') {
            return "ORDER BY {$order_by} {$order} ";
        }

        return "ORDER BY {$order_by} ";
    }

    /**
     * @see SqlInterface::groupBy()
     * {@inheritdoc}
     */
    public function groupBy(string $group_by) : string
    {
        return 'GROUP BY ' . $this->escapeColumn($group_by) . ' ';
    }

    /**
     * @see SqlInterface::limit()
     * {@inheritdoc}
     */
    public function limit(int $count, ?int $offset = null) : string
    {
        if (!$count) {
            return '';
        }

        $sql = "LIMIT {$count} ";
        if ($offset !== null) {
            $sql.= "OFFSET {$offset} ";
        }

        return $sql;
    }

    /**
     * @see SqlInterface::offset()
     * {@inheritdoc}
     */
    public function offset(int $offset) : string
    {
        return "OFFSET {$offset} ";
    }
}
