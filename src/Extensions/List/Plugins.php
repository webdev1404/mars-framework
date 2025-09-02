<?php
/**
* The Plugins List Reader Class
* @package Mars
*/

namespace Mars\Extensions\List;

use Mars\App\Kernel;
use Mars\Extensions\Module;

/**
 * The Plugins List Reader Class
 * Reads the list of plugins of this type from the system, which are enabled
 */
class Plugins
{
    use Kernel;

    /**
     * Returns the list of enabled plugins, found in the system
     * @return array The list of enabled plugins
     */
    public function get() : array
    {
        $list = [];
        $modules_list = Module::getList();

        foreach ($modules_list as $module_path) {
            $module_name = basename($module_path);
            $plugins_dir = $module_path . '/' . Module::DIRS['plugins'];

            if (is_dir($plugins_dir)) {
                $plugins = $this->app->dir->getFiles($plugins_dir, false, false, [], ['php']);

                if (!$plugins) {
                    continue;
                }

                foreach ($plugins as $plugin) {
                    $plugin_name = $this->app->file->getStem($plugin);
                    $class_name = Module::getPluginNamespace($module_name, $plugin_name);

                    //remove the starting slash from the class name
                    $class_name = ltrim($class_name, '\\');

                    $list[$class_name] = $module_name;
                }

            }
        }

        return $list;
    }
}
