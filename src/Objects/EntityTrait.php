<?php
/**
* The Entity Trait
* @package Mars
*/

namespace Mars\Objects;

use Mars\App;
use Mars\App\Kernel;
use Mars\Alerts\Errors;
use Mars\Validation\ValidateTrait;

/**
 * The Entity Trait
 * Contains the functionality of a basic object
 */
trait EntityTrait
{
    use Kernel;
    use ValidateTrait;

    /**
     * @var Errors $errors The generated errors, if any
     */
    public protected(set) Errors $errors {
        get {
            if (isset($this->errors)) {
                return $this->errors;
            }

            $this->errors = new Errors($this->app);

            return $this->errors;
        }
    }

    /**
     * Builds an object
     * @param array|object $data The entity's data
     */
    public function __construct(array|object $data = [], ?App $app = null)
    {
        $this->app = $app ?? App::obj();

        $this->set($data);
    }

    /**
     * Returns the object's id
     * @return int
     */
    public function getId() : int
    {
        return 0;
    }

    /**
     * Returns true if the object has the property set
     * @param string $name The name of the property
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->$name);
    }

    /**
     * Sets the object's properties
     * @param array|object $data The data
     * @param bool $overwrite If true, the data will overwrite the existing properties, if the properties already exist
     * @return static
     */
    public function set(array|object $data, bool $overwrite = true) : static
    {
        if (!$data) {
            return $this;
        }

        foreach ($data as $name => $val) {
            $name = trim($name);

            if (static::$frozen_fields && in_array($name, static::$frozen_fields)) {
                continue;
            }

            if (!$overwrite && isset($this->$name)) {
                continue;
            }

            $this->$name = $val;
        }

        return $this;
    }

    /**
     * Adds $data, if it doesn't already exist. Equivalent to set with $overwrite = false
     * @param array|object $data The data
     * @return static
     */
    public function add(array|object $data) : static
    {
        return $this->set($data, false);
    }

    /**
     * Alias for set
     * @see \Mars\Entity::set
     * {@inheritdoc}
     */
    public function assign(array|object $data) : static
    {
        return $this->set($data);
    }

    /**
     * Returns the object properties as an array
     * @param array $properties Array listing the properties which should be returned. If empty, all properties of the object are returned
     * @return array The object's data/properties
     */
    public function get(array $properties = []) : array
    {
        $data_array = App::getArray($this);

        //unset the errors property
        unset($data_array['errors']);

        if (!$properties) {
            return $data_array;
        }

        $data = [];
        foreach ($properties as $name) {
            if (isset($data_array[$name])) {
                $data[$name] = $data_array[$name];
            }
        }

        return $data;
    }

    /**
     * Binds the data from $data to the entity's properties
     * @param array $data The data to bind. If empty, the $_POST data is used
     * @param array $ignore_properties Array listing the properties from $data which shouldn't be included in the returned result
     * @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
     * @return $this
     */
    public function bind(array $data = [], ?array $ignore_properties = null, ?string $ignore_value = null) : static
    {
        $data = $this->getBindData($data);

        foreach ($data as $key => $value) {
            if (!isset($this->$key)) {
                continue;
            }

            if ($ignore_properties) {
                if (in_array($key, $ignore_properties)) {
                    continue;
                }
            }

            if ($ignore_value) {
                if ($value === $ignore_value) {
                    continue;
                }
            }

            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Binds the data from $data to the object's properties     
     * @param array $allowed_properties Array listing the properties which should be bound
     * @param array $data The data to bind
     * @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
     * @return $this
     */
    public function bindList(array $allowed_properties, array $data = [], ?string $ignore_value = null) : static
    {
        foreach ($allowed_properties as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            if (!isset($this->$key)) {
                continue;
            }

            $value = $data[$key];

            if ($ignore_value) {
                if ($value === $ignore_value) {
                    continue;
                }
            }

            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Returns the data to bind to the entity's properties
     * @param array $data The data to bind. If empty, the $_POST data is used
     * @return array The data to bind
     */
    protected function getBindData(array $data) : array
    {
        if ($data) {
            return $data;
        }

        return $this->app->request->post->getAll();
    }

    /**
     * Clears the entity's properties
     * @return static
     */
    public function reset() : static
    {
        $properties = App::getObjectProperties($this);

        foreach ($properties as $name => $value) {
            if (is_string($value)) {
                $this->$name = '';
            } elseif (is_int($value) || is_float($value)) {
                $this->$name = 0;
            } elseif (is_bool($value)) {
                $this->$name = false;
            } elseif (is_array($value)) {
                $this->$name = [];
            }
        }

        return $this;
    }
}
