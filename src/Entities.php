<?php
/**
* The Entities Class
* @package Mars
*/

namespace Mars;

use Mars\Objects\EntitiesTrait;

/**
 * The Entities Class
 * Container of multiple objects
 */
class Entities implements \Countable, \IteratorAggregate
{
    use EntitiesTrait;

    /**
     * @var string $class The class of the loaded objects
     */
    protected static string $class = \Mars\Entity::class;
}
