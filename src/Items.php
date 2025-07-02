<?php
/**
* The Items Class
* @package Mars
*/

namespace Mars;

use Mars\Objects\ItemsTrait;

/**
 * The Items Class
 * Container of multiple items
 * The classes extending Items must set these properties:
 * protected static $table = '';
 * protected static $id_field = '';
 */
abstract class Items extends Entities
{
    use ItemsTrait;

    /**
     * @var string|array $fields The database fields to load
     */
    public string|array $fields = '*';

    /**
     * @var string $table The table from which the objects will be loaded
     */
    protected static string $table = '';

    /**
     * @var string $id_field The id column of the table from which the objects will be loaded
     */
    protected static string $id_field = 'id';
}
