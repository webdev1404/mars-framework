<?php
/**
* The Modules Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App\HiddenProperty;
use Mars\Cache\Cacheable;
use Mars\Extensions\Setup\Module as ModuleSetup;

/**
 * The Modules Class
 */
class Modules extends Extensions
{
    /**
     * @internal
     */
    protected static array $supports = ['bin', 'config', 'languages', 'routes'];

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
    protected static string $list_config_file = 'modules/list.php';

    /**
     * @internal
     */
    protected static string $instance_class = Module::class;

    /**
     * @internal
     */
    protected static string $setup_class = ModuleSetup::class;

    /**
     * @internal
     */
    #[HiddenProperty]
    public Cacheable $cache {
        get => $this->app->cache->modules;
    }
}
