<?php
/**
* The Item Class
* @package Mars
*/

namespace Mars;

use Mars\Data\ItemTrait;


use Mars\Validation\ValidateTrait;

/**
 * The Item Class
 * The classes extending Item must set these properties:
 * protected static $table = '';
 * protected static $id_field = '';
 * protected static $name_field = '';
 */
#[\AllowDynamicProperties]
abstract class Item extends Entity
{
    use ItemTrait;

    /**
     * @var string|array $fields The database fields to load
     */
    public string|array $fields = '*';

    /**
     * @var string $table The table from which the object will be loaded.
     */
    protected static string $table = '';

    /**
     * @var string $id_field The id column of the table from which the object will be loaded
     */
    protected static string $id_field = 'id';

    /**
     * @var string $name_field The name column of the table from which the object will be loaded
     */
    protected static string $name_field = 'name';

    /**
     * @var array $ignore Array listing the custom properties (not found in the corresponding db table) which should be ignored when inserting/updating
     */
    protected static array $ignore = [];
    
    /**
     * @var bool $bind_data If true, when saving the object, the data will be bound to the object's columns
     */
    protected static bool $bind_data = false;

    /**
     * @var bool $original_keep If false, no original data will be set
     */
    protected static bool $original_keep = true;

    /**
     * @var array $original_list If specified, only the properties in the list will be stored as original data
     */
    protected static array $original_list = [];

    /**
     * @var array $defaults The default properties
     */
    protected static array $default = [];

    /**
     * @var array $defaults_override The list of overrides, when generating the default properties
     */
    protected static array $default_override = [];

    /**
     * @var array $defaults_override The properties not to include on the list of default properties
     */
    protected static array $default_ignore = [];

    /**
     * @var int $default_int The default value for int/float properties
     */
    protected static int $default_int = 0;

    /**
     * @var string $default_char The default value for string properties
     */
    protected static string $default_char = '';

    /**
     * @var array $validation_rules Validation rules
     */
    protected static array $validation_rules = [];

    /**
     * @var array $validation_rules_to_skip Validation rules to skip when validating, if any
     */
    protected array $validation_rules_to_skip = [];

    /**
     * @var array $validation_error_strings Custom error strings
     */
    protected static array $validation_error_strings = [];

    /**
     * @var array $frozen_fields Fields which cannot be changed by set()
     */
    protected static array $frozen_fields = ['fields', 'original', 'validation_rules_to_skip'];
}
