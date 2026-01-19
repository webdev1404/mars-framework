<?php
/**
* The System's Modules Class
* @package Mars
*/

namespace Mars\System;

use Mars\Extensions\Modules\Module;
use Mars\Extensions\Modules\Modules as BaseModules;

/**
 * The System's Modules Class
 */
class Modules extends BaseModules
{
    /**
     * @var array $instances The instances of the loaded modules
     */
    protected array $instances = [];

    /**
     * Returns a new module instance
     * @param string $name The name of the module
     * @param array $params Optional parameters to pass to the module constructor
     * @return Module The module
     */
    public function get(string $name, array $params = [], bool $use_cache = true) : Module
    {
        if (!$use_cache) {
            return new static::$instance_class($name, $params, $this->app);
        }

        $key = $name;
        if ($params) {
            $key .= ':' . md5(serialize($params));
        }
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        $this->instances[$key] = new static::$instance_class($name, $params, $this->app);

        return $this->instances[$key];
    }

    /**
     * Boots the enabled modules
     */
    public function boot()
    {
        $list = $this->getBootList();
        foreach ($list as $name) {
            $module = $this->get($name, use_cache: false);
            $module->boot();
        }
    }

    /**
     * Returns the list of modules to boot
     */
    protected function getBootList(): array
    {
        $cache_filename = 'modules-boot-list';

        $list = $this->app->cache->data->get($cache_filename);
        if ($this->app->development) {
            $list = null;
        }

        if ($list !== null) {
            return $list;
        }

        $list = [];
        $modules = $this->getEnabled();

        foreach ($modules as $name => $module) {
            $boot_filename = $module . '/boot.php';
            if (is_file($boot_filename)) {
                $list[] = $name;
            }
        }

        $this->app->cache->data->set($cache_filename, $list);

        return $list;
    }
}
