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
     * @var array|null $list The list of enabled extensions of this type
     */
    protected static ?array $list = null;

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
     * @var string $base_dir The base directory for the extension
     */
    protected static string $base_dir = '';

    /**
     * Determines if an extension is enabled
     * @param string $name The name of the extension
     * @return bool True if the extension is enabled, false otherwise
     */
    public function isEnabled(string $name): bool
    {
        static::$list ??= $this->get();

        return isset(static::$list[$name]);
    }

    /**
     * Gets the path of the specified extension
     * @param string $name The name of the extension
     * @return string|null The path of the extension, or null if not found
     */
    public function getPath(string $name): ?string
    {
        static::$list ??= $this->get();

        return static::$list[$name] ?? null;
    }

    /**
     * Returns the info array for the specified extension
     * @param string $name The name of the extension
     * @return array The info array
     */
    /*public static function getInfo(string $name) : array
    {
        $path = static::getPath($name);
        if (!$path) {
            //look in all extensions, if we didn't find it in the enabled ones
            $all_list = static::getAllList();
            $path = $all_list[$name] ?? null;

            if (!$path) {
                return [];
            }
        }

        $info_filename = $path . '/info.php';
        if (!is_file($info_filename)) {
            return [];
        }

        return include($info_filename);
    }*/

    /**
     * Gets the list of enabled extensions of this type
     * @return array The list of enabled extensions
     */
    public function get(): array
    {
        static::$list ??= $this->getAll();

        return static::$list;
    }

    /**
     * Gets the list of all available extensions of this type found on the disk
     * @param bool $use_cache If true, the cache will be used
     * @return array The list of all available extensions
     */
    public function getAll(bool $use_cache = true): array
    {
        if (static::$list_all !== null) {
            return static::$list_all;
        }

        $cache_filename = static::$base_dir . '-extensions-list-all';

        static::$list_all = $this->getList($use_cache, $cache_filename, function () {
            return $this->readAll();
        });

        return static::$list_all;
    }

    /**
     * Gets the list of available (not enabled) extensions of this type
     * @return array The list of available extensions
     */
    public function getAvailable(): array
    {
        $list_enabled = $this->getEnabled();
        $list_all = $this->getAll();

        return array_diff_key($list_all, $list_enabled);
    }

    /**
     * Gets the list of enabled extensions of this type
     * @param bool $use_cache If true, the cache will be used
     * @return array The list of enabled extensions
     */
    public function getEnabled(bool $use_cache = true): array
    {
        if (static::$list_enabled !== null) {
            return static::$list_enabled;
        }

        $cache_filename = static::$base_dir . '-extensions-list-enabled';

        static::$list_enabled = $this->getList($use_cache, $cache_filename, function () {
            $list_all = $this->getAll();

            if (static::$list_config_file) {
                $list_enabled = $this->app->config->read(static::$list_config_file);

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
        $list = $this->app->cache->getArray($cache_filename, false);

        // If we are in development mode, we always read the list from the filesystem
        $development = $this->app->development ? true : $this->app->config->development_extensions[static::$base_dir] ?? false;
        if ($development || !$use_cache) {
            $list = null;
        }

        if ($list) {
            return $list;
        }

        $list = $get();

        $this->app->cache->setArray($cache_filename, $list, false);

        return $list;
    }

    /**
     * Reads the list of all available extensions of this type from the disk
     * @return array The list of all available extensions
     */
    protected function readAll(): array
    {
        $dirs = array_merge($this->readFromVendor(static::$base_dir), $this->readFromExtensionsDir(static::$base_dir));

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
}
