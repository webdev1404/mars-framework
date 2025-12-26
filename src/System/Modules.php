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
     * Boots the enabled modules
     */
    public function boot()
    {
        $app = $this->app;

        $list = $this->getBootList();
        foreach ($list as $name) {
            $module = $this->get($name);
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

        foreach ($modules as $name =>$module) {
            $boot_filename = $module . '/boot.php';
            if (is_file($boot_filename)) {
                $list[] = $name;
            }
        }

        $this->app->cache->data->set($cache_filename, $list);

        return $list;
    }
}
