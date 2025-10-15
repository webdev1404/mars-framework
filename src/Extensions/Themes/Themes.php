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
    protected static ?array $list = null;

    /**
     * @internal
     */
    protected static ?array $list_enabled = null;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $list_config_file = '';

    /**
     * @internal
     */
    protected static string $base_dir = 'themes';
}
