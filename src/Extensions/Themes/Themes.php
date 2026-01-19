<?php
/**
* The Themes Class
* @package Mars
*/

namespace Mars\Extensions\Themes;

use Mars\Extensions\Extensions;

/**
 * The Themes Class
 */
class Themes extends Extensions
{
    /**
     * @internal
     */
    protected static bool $list_use_all = true;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $instance_class = Theme::class;
}
