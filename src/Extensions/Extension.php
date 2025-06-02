<?php
/**
* The Base Extension Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Base Extension Class
 * Base class for all basic extensions
 */
abstract class Extension
{
    use InstanceTrait;

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

            $this->path = $this->getRootPath() . '/' . static::$base_dir;
            if ($this->name) {
                $this->path.= '/' . $this->name;
            }

            return $this->path;
        }
    }

    /**
     * @var string $path_url The url pointing to the folder where the extension is located
     */
    public protected(set) string $url {
        get {
            if (isset($this->url)) {
                return $this->url;
            }

            $this->url = $this->getRootUrl() . '/' . rawurlencode(static::$base_dir);
            if ($this->name) {
                $this->url.= '/' . rawurlencode($this->name);
            }

            return $this->url;
        }
    }

    /**
     * @var string $url_static The static url pointing to the folder where the extension is located
     */
    public protected(set) string $url_static {
        get {
            if (isset($this->url_static)) {
                return $this->url_static;
            }

            $this->url_static = $this->getRootUrlStatic() . '/' . rawurlencode(static::$base_dir);
            if ($this->name) {
                $this->url_static.= '/' . rawurlencode($this->name);
            }

            return $this->url_static;
        }
    }

    public protected(set) string $namespace {
        get {
            if (isset($this->namespace)) {
                return $this->namespace;
            }

            $this->namespace = $this->getRootNamespace() . '\\' . static::$base_namespace . '\\' . App::getClass($this->name);

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

            $this->development = $this->app->development;

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
     * @var string $type The type of the extension
     */
    //protected static string $type = '';

    /**
     * @var string $base_dir The dir where these type of extensions are located
     */
    //protected static string $base_dir = '';

    /**
     * Builds the extension
     * @param string $name The name of the exension
     * @param array $params The params passed to the extension, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        $this->app = $app ?? $this->getApp();

        $this->name = $name;
        $this->params = $params;
    }

    /**
     * Returns the root path of the extension
     * @return string
     */
    protected function getRootPath() : string
    {
        return $this->app->extensions_path;
    }

    /**
     * Returns the root url of the extension
     * @return string
     */
    protected function getRootUrl() : string
    {
        return $this->app->extensions_url;
    }

    /**
     * Returns the static root url of the extension
     * @return string
     */
    protected function getRootUrlStatic() : string
    {
        return $this->app->getStaticUrl('extensions_url');
    }

    /**
     * Returns the root namespace of the extension
     * @return string
     */
    protected function getRootNamespace() : string
    {
        return $this->app->extensions_namespace;
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

    public static function getBaseNamespace() : string
    {
        return static::$base_namespace;
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
