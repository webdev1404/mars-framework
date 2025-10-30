<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Extensions\Extensions;
use Mars\Extensions\Modules\Module;
use Mars\Extensions\Modules\Modules;

/**
 * The Plugins Class
 */
class Plugins extends Extensions
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
    protected static string $list_config_file = '';

    /**
     * @internal
     */
    protected static string $instance_class = Plugin::class;

    /**
     * @see Extensions::readAll()
     * {@inheritdoc}
     */
    protected function readAll(): array
    {
        $list = [];

        $modules = new Modules($this->app);
        foreach ($modules->getEnabled() as $module_path) {
            $module_name = basename($module_path);
            $plugins_dir = $module_path . '/' . Module::DIRS['plugins'];
            if (!is_dir($plugins_dir)) {
                continue;
            }

            $plugins = $this->app->dir->getFiles($plugins_dir, false, false, [], ['php']);
            if (!$plugins) {
                continue;
            }

            $base_namespace = $this->app->modules->getBaseNamespace($module_name, Module::DIRS['plugins']);
            foreach ($plugins as $plugin) {
                $plugin_name = $this->app->file->getStem($plugin);
                $class_name = $base_namespace . '\\' . App::getClass($plugin_name);

                $list[$class_name] = $module_name;
            }
        }

        return $list;
    }
}
