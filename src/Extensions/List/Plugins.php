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
    public function get(string $type = '') : array
    {
        $list = [];
        $modules_list = $this->app->config->read('modules.php');

        foreach ($modules_list as $module_name) {
            $module = new Module($module_name, [], $this->app);
            $plugins_dir = $module->path . '/' . Module::DIRS['plugins'];
            
            if (is_dir($plugins_dir)) {
                $plugins = $this->app->dir->getFiles($plugins_dir, false, false, [], ['php']);

                if (!$plugins) {
                    continue;
                }

                foreach ($plugins as $plugin) {
                    $name = $this->app->file->getFile($plugin);
                    $class_name = $module->getPluginNamespace($name);

                    $list[$class_name] = $module_name;
                }

            }
        }

        return $list;
    }
}