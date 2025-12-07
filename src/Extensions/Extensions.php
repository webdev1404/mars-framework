<?php
/**
* The Base Extensions Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Base Extensions Class
 * Base class for all basic extensions objects
 */
abstract class Extensions
{
    use Kernel;

    /**
     * @var array|null $list_enabled The list of enabled extensions of this type
     */
    protected static ?array $list_enabled = null;

    /**
     * @var array|null $list_all The list of available extensions of this type
     */
    protected static ?array $list_all = null;

    /**
     * @var string $list_config_file The config file where the enabled extensions are listed
     */
    protected static string $list_config_file = '';

    /**
     * @var string $list_config_key The config key where the enabled extensions are listed
     */
    protected static string $list_config_key = '';

    /**
     * @var string $instance_class The class of the extensions instance
     */
    protected static string $instance_class = '';

    /**
     * Returns a new instance of the extensions manager
     * @param string $name The name of the extension
     * @param array $params Optional parameters to pass to the extension constructor
     * @return Extensions The extensions manager instance
     */
    public function get(string $name, array $params = []) : Extension
    {
        return new static::$instance_class($name, $params, $this->app);
    }

    /**
     * Determines if an extension exists
     * @param string $name The name of the extension
     * @return bool True if the extension exists, false otherwise
     */
    public function exists(string $name): bool
    {
        static::$list_all ??= $this->getAll();

        return isset(static::$list_all[$name]);
    }

    /**
     * Determines if an extension is enabled
     * @param string $name The name of the extension
     * @return bool True if the extension is enabled, false otherwise
     */
    public function isEnabled(string $name): bool
    {
        static::$list_enabled ??= $this->getEnabled();

        return isset(static::$list_enabled[$name]);
    }

    /**
     * Gets the path of the specified extension
     * @param string $name The name of the extension
     * @return string|null The path of the extension, or null if not found
     */
    public function getPath(string $name): ?string
    {
        static::$list_all ??= $this->getAll();

        return static::$list_all[$name] ?? null;
    }

    /**
     * Returns the base namespace for a module
     * @param string $name The name of the module
     * @param string $dir An optional subdir inside the module
     * @return string The base namespace for the module
     */
    public function getBaseNamespace(string $name, string $dir = ''): string
    {
        $namespace = static::$instance_class::getBaseNamespace() . '\\' . App::getClass($name);
        
        if ($dir) {
            $namespace .= '\\' . App::getClass($dir);
        }

        return $namespace;
    }

    /**
     * Returns the info array for the specified extension
     * @param string $name The name of the extension
     * @return array The info array
     */
    public function getInfo(string $name) : array
    {
        $list = $this->getAll();
        
        $path = $list[$name] ?? null;
        if (!$path) {
            return [];
        }

        $info_filename = $path . '/info.php';
        if (!is_file($info_filename)) {
            return [];
        }

        return include($info_filename);
    }

    /**
     * Gets the list of all available extensions of this type found on the disk
     * @param bool $use_cache If true, the cache will be used
     * @return array The list of all available extensions
     */
    public function getAll(bool $use_cache = true): array
    {
        if ($use_cache && static::$list_all !== null) {
            return static::$list_all;
        }

        $cache_filename = static::$instance_class::getBaseDir() . '-extensions-list-all';

        static::$list_all = $this->getList($use_cache, $cache_filename, function () {
            return $this->readAll();
        });

        return static::$list_all;
    }

    /**
     * Gets the list of available (not enabled) extensions of this type
     * @param bool $use_cache If true, the cache will be used
     * @return array The list of available extensions
     */
    public function getAvailable(bool $use_cache = true): array
    {
        $list_enabled = $this->getEnabled($use_cache);
        $list_all = $this->getAll($use_cache);

        return array_diff_key($list_all, $list_enabled);
    }

    /**
     * Gets the list of enabled extensions of this type
     * @param bool $use_cache If true, the cache will be used
     * @return array The list of enabled extensions
     */
    public function getEnabled(bool $use_cache = true): array
    {
        if ($use_cache && static::$list_enabled !== null) {
            return static::$list_enabled;
        }

        $cache_filename = static::$instance_class::getBaseDir() . '-extensions-list-enabled';

        static::$list_enabled = $this->getList($use_cache, $cache_filename, function () {
            $list_all = $this->getAll();

            if (static::$list_config_file) {
                //read the enabled extensions from the config file
                $list_enabled = $this->app->config->read(static::$list_config_file);

                return array_filter($list_all, fn ($extension) => in_array($extension, $list_enabled), ARRAY_FILTER_USE_KEY);
            } elseif (static::$list_config_key) {
                //read the enabled extensions from the config key
                $list_enabled = $this->app->config->get(static::$list_config_key) ?? [];

                return array_filter($list_all, fn ($extension) => in_array($extension, $list_enabled), ARRAY_FILTER_USE_KEY);
            }

            return $list_all;

        });

        return static::$list_enabled;
    }

    /**
     * Gets the list of extensions, using the cache if available
     * @param bool $use_cache If true, the cache will be used
     * @param string $cache_filename The cache filename
     * @param callable $get The function to call to get the list if not in cache
     * @return array The list of extensions
     */
    protected function getList(bool $use_cache, string $cache_filename, callable $get) : array
    {
        $list = $this->app->cache->get($cache_filename);

        // If we are in development mode, we always read the list from the filesystem
        $development = $this->app->development ? true : $this->app->config->development->extensions[static::$instance_class::getBaseDir()] ?? false;
        if ($development || !$use_cache) {
            $list = null;
        }

        if ($list) {
            return $list;
        }

        $list = $get();

        $this->app->cache->set($cache_filename, $list, false);

        return $list;
    }

    /**
     * Reads the list of all available extensions of this type from the disk
     * @return array The list of all available extensions
     */
    protected function readAll(): array
    {
        $dirs = array_merge($this->readFromVendor(static::$instance_class::getBaseDir()), $this->readFromExtensionsDir(static::$instance_class::getBaseDir()));

        $list = [];
        foreach ($dirs as $dir) {
            $name = basename($dir);

            $list[$name] = $dir;
        }

        return $list;
    }

    /**
     * Returns the list of extensions from the specified directory
     * @param string $dir The directory to scan
     * @param bool $check_info If true, we'll check for the info.php file in each directory
     * @return array The list of extensions
     */
    protected function readFromDir(string $dir, bool $check_info) : array
    {
        $dirs = $this->app->dir->getDirs($dir, false, true);
        if (!$check_info) {
            return $dirs;
        }

        foreach ($dirs as $key => $dir) {
            $info_file = $dir . '/info.php';

            if (!is_file($info_file)) {
                unset($dirs[$key]);
            }
        }

        return $dirs;
    }

    /**
     * Returns the list of extensions of the specified type, found in the extensions directory
     * @param string $type The type of the extensions
     * @return array The list of extensions
     */
    protected function readFromExtensionsDir(string $type) : array
    {
        return $this->readFromDir($this->app->extensions_path . '/' . $type, false);
    }

    /**
     * Scans the vendor directory for extensions of the specified type
     * @param string $type The type of the extensions
     * @return array The list of extensions
     */
    protected function readFromVendor(string $type) : array
    {
        $vendor_exclude = ['bin', 'psr', 'composer'];

        $dirs = [];

        $vendors = $this->app->dir->getDirs($this->app->vendor_path, false, true, $vendor_exclude);
        foreach ($vendors as $dir) {
            $vendor_packages = $this->app->dir->getDirs($dir, false, true);

            foreach ($vendor_packages as $package_dir) {
                $package_dir = $package_dir . '/' . $type;
                if (!is_dir($package_dir)) {
                    continue;
                }

                $dirs = array_merge($dirs, $this->readFromDir($package_dir, true));
            }
        }

        return $dirs;
    }

    /**
     * Installs the specified extension
     * @param string $name The name of the extension
     */
    public function install(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'install')) {
            $setup->install();
        }
       
        $this->createSymlink($extension);
    }

    /**
     * Enables the specified extension
     * @param string $name The name of the extension
     */
    public function enable(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'enable')) {
            $setup->enable();
        }

        $this->addConfig($name);
       
        $this->createSymlink($extension);
    }

    /**
     * Disables the specified extension
     * @param string $name The name of the extension
     */
    public function disable(string $name)
    {
        $extension = $this->get($name);
        if (!$extension->enabled) {
            throw new \Exception("Extension '{$name}' is not enabled.");
        }

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'disable')) {
            $setup->disable();
        }

        $this->removeConfig($name);
       
        $this->removeSymlink($extension);
    }

    /**
     * Upgrades the specified extension
     * @param string $name The name of the extension
     */
    public function upgrade(string $name)
    {
        $extension = $this->get($name);
        if (!$extension->enabled) {
            throw new \Exception("Extension '{$name}' is not enabled.");
        }

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'upgrade')) {
            $setup->upgrade();
        }

        $this->createSymlink($extension);
    }

    /**
     * Uninstalls the specified extension
     * @param string $name The name of the extension
     */
    public function uninstall(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'uninstall')) {
            $setup->uninstall();
        }

        $this->removeSymlink($extension);
    }

    /**
     * Adds the specified extension to the config file listing the enabled extensions
     */
    protected function addConfig(string $name)
    {
        if (!static::$list_config_file) {
            return;
        }

        $extensions = $this->app->config->read(static::$list_config_file);
        
        $extensions[] = $name;
        $extensions = array_unique($extensions);

        $this->app->config->write(static::$list_config_file, $extensions);
    }

    /**
     * Removes the specified extension from the config file listing the enabled extensions
     */
    protected function removeConfig(string $name)
    {
        if (!static::$list_config_file) {
            return;
        }

        $extensions = $this->app->config->read(static::$list_config_file);
        
        $extensions = array_filter($extensions, fn ($extension) => $extension !== $name);

        $this->app->config->write(static::$list_config_file, $extensions);
    }

    /**
     * Returns the setup manager for the specified extension
     * @param string $name The name of the extension
     * @return object|null The setup manager object, or null if not found
     */
    protected function getSetupManager(string $name) : ?object
    {
        $setup_file = $this->getPath($name) . '/' . static::$instance_class::DIRS['setup'] . '/Setup.php';
        if (!is_file($setup_file)) {
            return null;
        }

        $setup_class = $this->getBaseNamespace($name, static::$instance_class::DIRS['setup']) . '\\Setup';

        return new $setup_class($this->app);
    }

    /**
     * Creates a symlink to the assets folder in the public directory
     * @param Extension $extension The extension object
     */
    protected function createSymlink(Extension $extension)
    {
        if (!is_dir($extension->assets_path)) {
            return;
        }
        if (is_link($extension->assets_target)) {
            return;
        }

        symlink($extension->assets_path, $extension->assets_target);

        if (!is_link($extension->assets_target)) {
            throw new \Exception("Failed to create symlink for assets folder: {$extension->assets_target}");
        }
    }

    /**
     * Removes the symlink to the assets folder in the public directory
     * @param Extension $extension The extension object
     */
    protected function removeSymlink(Extension $extension)
    {
        if (is_link($extension->assets_target)) {
            unlink($extension->assets_target);
        }
    }
}
