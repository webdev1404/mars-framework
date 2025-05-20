<?php
/**
* The Entity Class
* @package Mars
*/

namespace Mars;

use Mars\Data\EntityTrait;

/**
 * The Entity Class
 * Contains the functionality of a basic object
 */
#[\AllowDynamicProperties]
class Entity
{
    use EntityTrait;

    /**
     * @var array $frozen_fields Fields which cannot be changed by set()
     */
    protected static array $frozen_fields = [];
}
