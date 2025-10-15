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

            $this->path = $this->manager->getPath($this->name);

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

            $this->namespace =  static::$base_namespace . '\\' . App::getClass($this->name);

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
     * @var Extensions $manager The extensions manager object
     */
    public protected(set) Extensions $manager {
        get {
            if (isset($this->manager)) {
                return $this->manager;
            }

            if (static::$manager_instance === null) {
                $class_name = $this->manager_class;
                static::$manager_instance = new $class_name($this->app);
            }

            $this->manager = static::$manager_instance;

            return $this->manager;
        }
    }

    /**
     * @var string $manager_class The class of the extensions manager
     */
    protected string $manager_class = '';

    /**
     * @var Extensions|null $manager_instance The instance of the extensions manager
     */
    protected static ?Extensions $manager_instance = null;

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
        $this->name = $name;
        $this->params = $params;
        $this->app = $app;

        if (!$this->manager->isEnabled($this->name)) {
            throw new \Exception("Extension '{$this->name}' of type '" . static::$type . "' not found. It either does not exist or is not enabled.");
        }
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
    public static function getBaseNamespace() : string
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
