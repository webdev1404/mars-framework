<?php
/**
* The Modules Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Cache\Cacheable;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;

/**
 * The Modules Class
 */
class Modules extends Extensions
{
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
    public Cacheable $cache {
        get => $this->app->cache->modules;
    }

    /**
     * @see Extensions::install()
     */
    public function install(string $name) : Extension
    {
        $extension = parent::install($name);

        if (!$extension->enabled) {
            $this->addConfig($name);
        }

        return $extension;
    }

    /**
     * @see Extensions::uninstall()
     */
    public function uninstall(string $name) : Extension
    {
        $extension = parent::uninstall($name);

        if ($extension->enabled) {
            $this->removeConfig($name);
        }

        return $extension;
    }
}
