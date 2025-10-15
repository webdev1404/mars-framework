<?php
/**
* The Modules Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\Extensions\Extensions;

/**
 * The Modules Class
 */
class Modules extends Extensions
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
    protected static string $list_config_file = 'modules.php';

    /**
     * @internal
     */
    protected static string $base_dir = 'modules';

    /**
     * @internal
     */
    public function get(): array
    {
        static::$list ??= $this->getEnabled();

        return static::$list;
    }
}
