<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\App\HiddenProperty;
use Mars\Cache\Cacheable;
use Mars\Extensions\Setup\Plugin as PluginSetup;

/**
 * The Plugins Class
 */
class Plugins extends Extensions
{
    /**
     * @internal
     */
    protected static array $supports = ['bin', 'config', 'languages', 'routes', 'sitemaps'];

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
    protected static string $list_config_file = 'plugins/list.php';

    /**
     * @internal
     */
    protected static string $instance_class = Plugin::class;

    /**
     * @internal
     */
    protected static string $setup_class = PluginSetup::class;

    /**
     * @internal
     */
    #[HiddenProperty]
    public Cacheable $cache {
        get => $this->app->cache->plugins;
    }
}
