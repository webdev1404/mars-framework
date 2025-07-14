<?php
/**
* The Base Extension Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\App\Kernel;
use Mars\Extensions\List\Reader;

/**
 * The Base Extension Class
 * Base class for all basic extensions
 */
abstract class Extension
{
    use Kernel;
    
    /**
     * @const array DIR The locations of the used extensions subdirs
     */
    public const array DIRS = [
        'assets' => 'assets',
        'setup' => 'setup',
    ];

    /**
     * @var string $name The name of the extension
     */
    public protected(set) string $name = '';

    /**
     * @var string $path The path where the extension is located
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }
            
            $this->path = static::$available_list[$this->name] ?? '';

            return $this->path;
        }
    }

    /**
     * @var string $assets_path The folder where the assets files are stored
     */
    public protected(set) string $assets_path {
        get {
            if (isset($this->assets_path)) {
                return $this->assets_path;
            }

            $this->assets_path = $this->path . '/' . static::DIRS['assets'];

            return $this->assets_path;
        }
    }

    /**
     * @var string $assets_url The url pointing to the folder where the assets for the extension are located
     */
    public protected(set) string $assets_url {
        get {
            if (isset($this->assets_url)) {
                return $this->assets_url;
            }

            $this->assets_url = $this->app->assets_url . '/' . rawurlencode(static::$base_dir) . '/' . rawurlencode($this->name);

            return $this->assets_url;
        }
    }

    /**
     * @var string $assets_target The path of the assets folder, in the public directory, where the assets for this extension are located.
     */
    public protected(set) string $assets_target {
        get {
            if (isset($this->assets_target)) {
                return $this->assets_target;
            }

            $this->assets_target = $this->app->assets_path . '/' . static::$base_dir . '/' . $this->name;

            return $this->assets_target;
        }
    }

    /**
     * @var string $namespace The namespace of the extension
     */
    public protected(set) string $namespace {
        get {
            if (isset($this->namespace)) {
                return $this->namespace;
            }

            $this->namespace =  $this->getBaseNamespace() . '\\' . App::getClass($this->name);

            return $this->namespace;
        }
    }

    /**
     * @var bool $development If true, the extension is run in development mode
     */
    public bool $development {
        get {
            if (isset($this->development)) {
                return $this->development;
            }

            $this->development = $this->app->development ? true : $this->app->config->development_extensions[static::$base_dir] ?? false;

            return $this->development;
        }
    }

    /**
     * @var array $params The parames passed to the extension, if any
     */
    public protected(set) array $params = [];

    /**
     * @var float $exec_time The time needed to run this extension
     */
    public float $exec_time = 0;

    /**
     * @var array|null $list The list of enabled extensions of this type
     */
    protected static ?array $enabled_list = null;

    /**
     * @var string $enabled_list_config_file The config file where the enabled extensions are listed
     */
    protected static string $enabled_list_config_file = '';

    /**
     * @var array|null $available_list The list of available extensions of this type
     */
    protected static ?array $available_list = null;

    /**
     * @var string $type The type of the extension
     */
    protected static string $type = '';

    /**
     * @var string $base_dir The dir where these type of extensions are located
     */
    protected static string $base_dir = '';

    /**
     * @var string $base_namespace The base namespace for this type of extension
     */
    protected static string $base_namespace = '';

    /**
     * Builds the extension
     * @param string $name The name of the extension
     * @param array $params The params passed to the extension, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        static::getAvailableList();
        static::getEnabledList();

        $this->app = $app ?? App::obj();
        $this->name = $name;
        $this->params = $params;

        if (!isset(static::$enabled_list[$this->name])) {
            throw new \Exception("Extension '{$this->name}' of type '" . static::$type . "' not found. It either does not exist or is not enabled.");
        }
    }

    /**
     * Returns the list of available extensions of this type
     * @return array The list of available extensions
     */
    public static function getAvailableList() : array
    {
        if (static::$available_list !== null) {
            return static::$available_list;
        }

        $app = static::getApp();

        $filename = static::getAvailableListFilename();

        static::$available_list = static::getListFromCache($filename, $app);
        if (static::$available_list !== null) {
            return static::$available_list;
        }

        static::$available_list = static::getListReader($app)->get(static::$base_dir);

        $app->cache->setArray($filename, static::$available_list, false);

        return static::$available_list;
    }

    /**
     * Returns the list of enabled extensions of this type
     * @return array The list of enabled extensions
     */
    public static function getEnabledList() : array
    {
        if (static::$enabled_list !== null) {
            return static::$enabled_list;
        }
        if (static::$available_list === null) {
            static::getAvailableList();
        }

        // If the we don't list the extensions in a config file, we use the available list as the enabled list
        if (!static::$enabled_list_config_file) {
            static::$enabled_list = static::$available_list;

            return static::$enabled_list;
        }

        $app = static::getApp();

        $filename = static::getEnabledListFilename();

        static::$enabled_list = static::getListFromCache($filename, $app);
        if (static::$enabled_list !== null) {
            return static::$enabled_list;
        }

        $config_list = $app->config->read(static::$enabled_list_config_file);
        $enabled_list = array_filter(static::$available_list, fn ($extension) => in_array($extension, $config_list), ARRAY_FILTER_USE_KEY);

        static::$enabled_list = static::getListData($enabled_list, $config_list);

        $app->cache->setArray($filename, static::$enabled_list, false);
  
        return static::$enabled_list;
    }

    /**
     * Returns the list of available extensions from the cache, or null if not found
     * @param string $filename The filename used to cache the list
     * @param App $app The app object
     * @return array|null The list of available extensions, or null if not found
     */
    protected static function getListFromCache(string $filename, App $app) : ?array
    {
        $list = $app->cache->getArray($filename, false);

        // If we are in development mode, we always read the list from the filesystem
        $development = $app->development ? true : $app->config->development_extensions[static::$base_dir] ?? false;
        if ($development) {
            return null;
        }

        return $list;
    }

    /**
     * Returns the filename used to cache the list of available extensions
     * @return string The filename
     */
    public static function getAvailableListFilename() : string
    {
        return static::$base_dir . '-available-extensions-list';
    }

    /**
     * Returns the filename used to cache the list of enabled extensions
     * @return string The filename
     */
    public static function getEnabledListFilename() : string
    {
        return static::$base_dir . '-enabled-extensions-list';
    }

    /**
     * Returns the reader object used to read the list of available extensions
     * @param App $app The app object
     * @return object The reader object
     */
    public static function getListReader($app) : object
    {
        return new Reader($app);
    }

    /**
     * Returns the data for the list of extensions
     * @param array $enabled_list The list of enabled extensions
     * @param array $config_list The config list of extensions
     * @return array The data for the list of extensions
     */
    protected static function getListData(array $enabled_list, array $config_list) : array
    {
        foreach ($enabled_list as $name => $path) {
            $enabled_list[$name] = [
                'path' => $path
            ];
        }

        return $enabled_list;
    }

    /**
     * Returns the path of the extension
     * @param string $name The name of the extension
     * @return string|null The path of the extension, or null if not found
     */
    public static function getPath(string $name) : ?string
    {
        if (static::$enabled_list === null) {
            static::$enabled_list = static::getEnabledList();
        }

        return static::$enabled_list[$name]['path'] ?? null;
    }

    /**
     * Returns the extension's type
     * @return string
     */
    public static function getType() : string
    {
        return static::$type;
    }

    /**
     * Returns the extension's base dir
     * @return string
     */
    public static function getBaseDir() : string
    {
        return static::$base_dir;
    }

    /**
     * Returns the base namespace for this type of extension
     * @return string The base namespace
     */
    public function getBaseNamespace() : string
    {
        return static::$base_namespace;
    }

    /**
     * Returns the list of existing files in the specified directory
     * @param string $dir The directory to scan for files
     * @param string $cache_filename The filename used to cache the list of files
     * @return array The list of existing files
     */
    protected function getExistingFiles(string $dir, string $cache_filename) : array
    {
        $files = $this->app->cache->getArray($cache_filename, false);

        // Force files scan if we are in development mode
        if ($this->development) {
            $files = null;
        }

        if ($files === null) {
            $files = $this->app->dir->get($dir, true, false);
            $files = $this->app->array->flip($files);

            $this->app->cache->setArray($cache_filename, $files, false);
        }

        return $files;
    }

    /**
     * Returns the setup class for this type of extension
     * @return string The setup class
     */
    /*public static function getSetupClass() : string
    {
        return static::$setup_class;
    }*/

    /**
     * Runs the extension and outputs the generated content
     */
    public function output()
    {
        echo $this->run();
    }

    /**
     * Executes the extension's code and returns the generated content
     * @return string The generated content
     */
    public function run() : string
    {
        $this->startOutput();

        include($this->path . '/index.php');

        return $this->endOutput();
    }

    /**
     * Starts the output buffering
     */
    protected function startOutput()
    {
        $this->app->timer->start('extension_output');

        ob_start();
    }

    /**
     * Ends the output buffering
     * @return string The output
     */
    protected function endOutput() : string
    {
        $output = ob_get_clean();

        $this->exec_time = $this->app->timer->stop('extension_output');

        return $output;
    }
}
