<?php
/**
* The Items Trait
* @package Mars
*/

namespace Mars\Objects;

use Mars\App;
use Mars\App\Kernel;
use Mars\HiddenProperty;
use Mars\Entity;
use Mars\Db;
use Mars\Db\Sql\Sql;

/**
 * The Items Trait
 * Container of multiple items
 * The classes extending Items must set these properties:
 * protected static $table = '';
 * protected static $id_field = '';
 */
trait ItemsTrait
{
    use Kernel;

    /**
     * @var Db $db The database object. Alias for $this->app->db
     */
    #[HiddenProperty]
    protected Db $db {
        get => $this->app->db;
    }

    /**
     * Builds the Items object
     * @param bool $load If true, will automatically load the items
     * @param App $app The app object
     */
    public function __construct(bool $load = false, ?App $app = null)
    {
        $this->app = $app ?? App::obj();

        if ($load) {
            $this->loadAll();
        }
    }

    /**
     * Unsets the app & db property when serializing
     */
    public function __sleep()
    {
        $data = get_object_vars($this);

        unset($data['app']);
        unset($data['db']);

        return array_keys($data);
    }

    /**
     * Returns the table name
     * @return string
     */
    public function getTable() : string
    {
        return static::$table;
    }

    /**
     * Returns the id field name
     * @return string The name of the id field
     */
    public function getIdField() : string
    {
        return static::$id_field;
    }

    /**
     * Returns the ids
     * @return array
     */
    public function getIds() : array
    {
        return array_keys($this->data);
    }

    /**
     * Loads all the objects
     * @return static
     */
    public function loadAll() : static
    {
        return $this->load();
    }

    /**
     * Loads the objects
     * @param array $where Where conditions in the format col => val
     * @param string $order_by The order by column
     * @param string $order The order: asc/desc
     * @param int $limit The limit
     * @param int $limit_offset The limit offset, if any
     * @return static
     */
    public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : static
    {
        $sql = $this->db->getSql()->select($this->fields)->from($this->getTable())->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

        return $this->loadBySql($sql);
    }

    /**
     * Loads objects using a sql query
     * @param string|Sql $sql The sql code used to load the objects
     * @return static
     */
    public function loadBySql(string|Sql $sql) : static
    {
        $this->data = [];

        $data = $this->db->readQuery($sql)->fetchAll(true);
        $this->set($data);

        return $this;
    }

    /**
     * Loads a set of objects based on ids
     * @param array $ids The ids of the objects to load
     * @return static
     */
    public function loadIds(array $ids) : static
    {
        $sql = $this->db->getSql()->select($this->fields)->from($this->getTable())->whereIn($this->getIdField(), $ids);

        return $this->loadBySql($sql);
    }

    /**
     * Loads a set of objects based on the based data. These keys might be specififed: where, order_by, order, limit, limit_offset
     * @param array $data The data used to build the sql object from
     * @return static
     */
    public function loadByData(array $data) : static
    {
        $where = $data['where'] ?? [];
        $order_by = $data['order_by'] ?? '';
        $order = $data['order'] ?? '';

        $sql = $this->db->getSql()->select($this->fields)->from($this->getTable())->where($where)->orderBy($order_by, $order);

        return $this->loadBySql($sql);
    }

    /**
     * Returns the total number of items from the table
     * @return int
     */
    public function getTotal() : int
    {
        return $this->db->count($this->getTable());
    }

    /**
     * Inserts an object into the db
     * @param array|object $data The data to insert
     * @return int The id of the newly added item
     */
    public function insert(array|object $data) : int
    {
        $obj = $this->getObject($data);
        $id = $obj->insert();
        if ($id) {
            $this->add($obj);
        }

        return $id;
    }

    /**
     * Deletes the specified IDs.
     * @param int|array $ids The IDs to delete. If null, all the current loaded objects will be deleted
     * @return int The number of affected rows
     */
    public function delete(int|array|null $ids = null) : int
    {
        $ids = (array)($ids ?? $this->getIds());
        if (!$ids) {
            return 0;
        }

        return $this->db->deleteByIds($this->getTable(), $ids, $this->getIdField());
    }

    /**
     * @see \Mars\Entities::getObject()
     * {@inheritdoc}
     */
    public function getObject(array|object $data) : Entity
    {
        if ($data instanceof Entity) {
            return $data;
        }

        $class_name = $this->getClass();
        return new $class_name($data, $this->app);
    }
}