<?php
/**
* The Item Trait
* @package Mars
*/

namespace Mars\Objects;

use Mars\App;
use Mars\HiddenProperty;
use Mars\Db;
use Mars\Db\Sql;

/**
 * The Item Trait
 * The classes using Item must set these properties:
 * protected static $table = '';
 * protected static $id_field = '';
 * protected static $name_field = '';
 */
trait ItemTrait
{
    /**
     * @var array $original Array containing the original properties
     */
    #[HiddenProperty]
    protected array $original = [];

    /**
     * @var Db $db The database object. Alias for $this->app->db
     */
    #[HiddenProperty]
    protected Db $db {
        get => $this->app->db;
    }

    /**
     * @var bool $loaded True if the item was loaded
     */
    protected bool $loaded = false;

    /**
     * Builds an item
     * @param mixed $data If data is an int, will load the data with id = data from the database. If string will load the data with name = data. If an array or object, will assume the array contains the object's data. If null, will load the defaults
     * @param App $app The app object
     */
    public function __construct($data = 0, ?App $app = null)
    {
        $this->app = $app;

        $table = $this->getTable();
        if (!$table) {
            throw new \Exception('The $table static property of class ' . get_class($this) . ' is not set!');
        }

        $this->setId(0);
        $this->load($data);
    }

    /**
     * Returns the table name
     * @return string The table name
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
     * Returns the name field
     * @return string The name of the name field
     */
    public function getNameField() : string
    {
        return static::$name_field;
    }

    /**
     * @see \Mars\Entity::add()
     * {@inheritDoc}
     */
    public function getId() : int
    {
        $id_field = $this->getIdField();

        return (int)($this->$id_field ?? 0);
    }

    /**
     * Sets the id
     * @return static
     */
    public function setId(int $id) : static
    {
        $id_field = $this->getIdField();

        $this->$id_field = $id;

        return $this;
    }

    /**
     * Determines if the object's id is set
     * @return bool
     */
    public function is() : bool
    {
        return (bool)$this->getId();
    }

    /**
     * @see \Mars\Entity::set()
     * {@inheritDoc}
     */
    public function set(array|object $data, bool $overwrite = true) : static
    {
        $this->original = $this->getOriginalData($this->app->array->get($data));

        parent::set($data, $overwrite);

        $this->prepare();

        return $this;
    }

    /**
     * Loads an object
     * @param mixed $data If data is an int, will load the data with id = data from the database. If string will load the data with name = data. If an array or object, will assume the array contains the object's data. If null, will load the defaults
     * @return static
     */
    public function load(mixed $data = null, bool $reload = false) : static
    {
        if ($this->loaded && !$reload) {
            return $this;
        }

        $overwrite = true;

        if ($data === null) {
            //load defaults
            $overwrite = false;
            $data = $this->getDefaultData();
        }

        if (!$data) {
            return $this;
        }

        if (is_numeric($data)) {
            //load the data from the db, by id
            $data = $this->getRowById($data);
        } elseif (is_string($data)) {
            //load the data from the db, by name
            $data = $this->getRowByName($data);
        }

        if (!$data) {
            return $this;
        }

        //set the data from the array/object
        $this->set($data, $overwrite);

        $this->loaded = true;

        return $this;
    }

    /**
     * Loads the object by id
     * @param int $id The id
     * @return static
     */
    public function loadById(int $id) : static
    {
        return $this->load($id);
    }

    /**
     * Loads the object by name
     * @param string $name The name
     * @return static
     */
    public function loadByName(string $name) : static
    {
        $data = $this->getRowByName($name);
        if (!$data) {
            return $this;
        }

        return $this->load($data);
    }

    /**
     * Loads the default data
     * @return static
     */
    public function loadDefaults() : static
    {
        $data = $this->getDefaultData();

        return $this->set($data);
    }

    /**
     * Returns the row from the database, based on id
     * @param int $id The id to return the data for
     * @return ?object The row, or null on failure
     */
    public function getRowById(int $id) : ?object
    {
        return $this->db->selectById($this->getTable(), $id, $this->getIdField());
    }

    /**
     * Returns the row from the database, based on name
     * @param string $name The name to return the data for
     * @return ?object The row, or null on failure
     */
    public function getRowByName(string $name) : ?object
    {
        return $this->db->selectRow($this->getTable(), [$this->getNameField() => $name]);
    }

    /**
     * Loads an object using a sql query
     * @param string|Sql $sql The sql code used to load the object
     * @return static
     */
    public function loadBySql(string|Sql $sql) : static
    {
        $data = $this->db->readQuery($sql)->fetchArray();
        if ($data === null) {
            //don't load the defaults, if an empty row is returned
            $data = 0;
        }

        return $this->load($data);
    }

    /**
     * Child classes can implement this method to process the object when it's loaded
     */
    protected function prepare()
    {
    }

    /**
     * Child classes can implement this method to process the object when it's inserted/updated
     */
    protected function process()
    {
    }

    /**
     * Child classes can implement this method to process the object when it's inserted
     */
    protected function processInsert()
    {
    }

    /**
     * Child classes can implement this method to process the object when it's updated
     */
    protected function processUpdate()
    {
    }

    /**
     * Returns the default data
     * @return array
     */
    protected function getDefaultData() : array
    {
        $class_name = static::class;

        if (!isset(static::$default[$class_name])) {
            static::$default[$class_name] = $this->db->fill($this->getTable(), static::$default_override ?? [], static::$default_ignore ?? [], static::$default_int ?? 0, static::$default_char ?? '', true);
        }

        return static::$default[$class_name];
    }

    /**
     * Inserts the object in the database
     * @return int The id of the newly inserted item
     */
    public function insert() : int
    {
        $this->processInsert();
        $this->process();

        if (!$this->validate()) {
            return 0;
        }

        $id = $this->db->insert($this->getTable(), $this->getData());

        $this->setId($id);

        return $id;
    }

    /**
     * Updates the object
     * @return int Returns the id of the updated item
     */
    public function update() : int
    {
        $this->processUpdate();
        $this->process();

        if (!$this->validate()) {
            return 0;
        }

        $this->db->updateById($this->getTable(), $this->getData(), $this->getId(), $this->getIdField());

        return $this->getId();
    }

    /**
     * Saves the data to the db. Calls insert if the id of the object is 0, update otherwise
     * @return int The id of the inserted/updated item
     */
    public function save() : int
    {
        $id = $this->getId();
        if ($id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Returns the data which will be used by an insert/update operation
     * Unsets the properties defined in static::$ignore, which shouldn't be stored when inserting/updating
     * @return array
     */
    protected function getData() : array
    {
        $data = $this->get();

        //unset the id field
        $id_field = $this->getIdField();
        unset($data[$id_field]);

        //unset the errors & fields property
        //unset($data['errors']);
        unset($data['fields']);

        $ignore = static::$ignore ?? [];
        if ($ignore) {
            $data = $this->app->array->unset($data, $ignore);
        }

        return $data;
    }

    /**
     * Deletes the object
     * @return int The number of affected rows
     */
    public function delete() : int
    {
        return $this->db->deleteById($this->getTable(), $this->getId(), $this->getIdField());
    }

    /**
     * Binds the data from $data to the object's properties
     * @see \Mars\Objects\EntityTrait::bind()
     * {@inheritDoc}
     */
    public function bind(array|object $data = [], ?array $ignore_columns = null, ?string $ignore_value = null, ?array $properties = null) : static
    {
        if (!$this->loaded) {
            $this->load();
        }

        $id_field = $this->getIdField();

        //if no ignore columns array are specified, include the id field automatically
        if ($ignore_columns === null) {
            $ignore_columns = [$id_field];
        }

        return parent::bind($data, $ignore_columns, $ignore_value, $properties);
    }

    /**
     * Binds the data from $data to the object's properties
     * @see \Mars\Objects\EntityTrait::bindList()
     * {@inheritDoc}
     */
    public function bindList(array $properties, array|object $data = [], ?string $ignore_value = null) : static
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::bind($data, null, $ignore_value, $properties);
    }

    /**
     * Returns true if the specified property is stored as original data
     * @param string $property The name of the property
     * @return bool
     */
    public function isOriginal(string $property) : bool
    {
        return isset($this->original[$property]);
    }

    /**
     * Returns the original data
     * @param string $property If specified, only this property will be returned
     * @return mixed The original value
     */
    public function getOriginal(string $property = '')
    {
        if ($property) {
            return $this->original[$property] ?? null;
        }

        return $this->original;
    }

    /**
     * Returns the original properties to be stored
     * @param array $data The data
     * @return array
     */
    protected function getOriginalData(array $data) : array
    {
        $original_keep = static::$original_keep ?? true;
        $original_list = static::$original_list ?? [];

        if (!$original_keep) {
            return [];
        }

        if ($original_list) {
            return $this->app->data->getProperties($data, $original_list);
        }

        return $data;
    }

    /**
     * Determines if a property is updatable.
     * The property is considered updatable if it's set and doesn't equal the original stored value (assuming the original value exists)
     * @param string $property The name of the property
     * @return bool
     */
    public function canUpdate(string $property) : bool
    {
        if (!isset($this->original[$property])) {
            return true;
        }

        return !($this->original[$property] == $this->$property);
    }

    /**
     * Flips the current value with the original value
     * @param string|array $properties The name of the properties to flip
     * @return static
     */
    public function flip(string|array $properties) : static
    {
        $properties = (array)$properties;

        foreach ($properties as $property) {
            if (!isset($this->original[$property])) {
                continue;
            }

            $value = $this->original[$property];

            $this->original[$property] = $this->$property;

            $this->$property = $value;
        }

        return $this;
    }
}
