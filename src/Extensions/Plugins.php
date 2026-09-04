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

    /**
     * @see Extensions::cache()
     * {@inheritDoc}
     */
    public function cache()
    {
        parent::cache();

        $this->getFiles();
    }

    /**
     * Gets all the class files for the enabled plugins
     */
    public function getFiles() : array
    {
        $cache_name = 'files-list';

        $files = $this->cache->get($cache_name);

        $development = $this->app->development ? true : $this->app->config->development->extensions[static::$instance_class::getBaseDir()] ?? false;
        if ($development) {
            $files = null;
        }

        if ($files !== null) {
            return $files;
        }

        $files = [];
        foreach ($this->getEnabled() as $name => $path) {
            $plugin_files = [];

            $src_files = $this->app->dir->getFiles($path . '/' . Plugin::DIRS['src']);
            foreach ($src_files as $file) {
                $plugin_files[] = Plugin::getNamespace($name) . '\\' . App::getClass($this->app->file->getStem($file));
            }
            
            $files[$name] = $plugin_files;

        }

        $this->cache->set($cache_name, $files);

        return $files;
    }
}
