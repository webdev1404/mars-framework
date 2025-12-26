<?php
/**
* The Config Class
* @package Mars
*/

namespace Mars;

use Mars\HiddenProperty;
use Mars\App\Info;
use Mars\Config\Container;
use Mars\Config\Defaults;
use Mars\Config\ArrayResult;

/**
 * The Config Class
 * Stores the system's config options
 */
#[\AllowDynamicProperties]
class Config extends Container
{
    use Info;

    /**
     * The app object
     * @var App
     */
    #[HiddenProperty]
    protected App $_app;

    /**
     * List of files to load at startup
     */
    protected $load_files = ['config.php', 'app.php'];

    /**
     * @var string $cache_filename The cache filename
     */
    protected string $cache_filename {
        get {
            if (isset($this->cache_filename)) {
                return $this->cache_filename;
            }

            $this->cache_filename = $this->_app->cache_path . '/config/config.php';

            return $this->cache_filename;
        }
    }

    /**
     * Builds the Config object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->_app = $app;

        $this->init();

        $this->normalize();

        $this->check();
    }

    /**
     * Reads the config settings from the config.php file and the autoload files
     * @return static
     */
    protected function init() : static
    {
        if ($this->loadFromCache()) {
            return $this;
        }

        $settings = Defaults::$settings;

        foreach ($this->load_files as $file) {
            $data = $this->readFilename($this->_app->config_path . '/' . $file);

            $settings = array_merge($settings, $data);
        }

        $this->assign($this->getTree($settings));

        $this->cache();

        return $this;
    }

    /**
     * Loads the config settings from cache
     * @return bool Whether the config was loaded from cache
     */
    protected function loadFromCache() : bool
    {
        if (!is_file($this->cache_filename)) {
            return false;
        }

        $properties = $this->readFilename($this->cache_filename);

        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    /**
     * Caches the config settings to a file
     */
    protected function cache()
    {
        if ($this->development->enable) {
            return;
        }
        
        $properties = $this->_app->object->getProperties($this);

        $content = "<?php\n\nreturn " . var_export($properties, true) . ";";

        file_put_contents($this->cache_filename, $content);
    }

    /**
     * Builds a nested tree from a flat array
     * @param array $data The flat array
     * @param string $separator The separator used in the keys
     * @return array The tree
     */
    public function getTree(array $data, string $separator = '.'): array
    {
        $tree = [];

        foreach ($data as $key => $value) {
            $parts = explode($separator, $key);
            $ref = &$tree;

            foreach ($parts as $part) {
                $ref[$part] ??= [];

                $ref = &$ref[$part];
            }

            if (is_array($value)) {
                $ref = new ArrayResult($value);
            } else {
                $ref = $value;
            }

            unset($ref);
        }

        return $tree;
    }

    /**
     * Gets a config option by its key. Eg: db.host
     * @param string $key The config key
     * @return mixed The config value or null if not found
     */
    public function get(string $key) : mixed
    {
        $parts = explode('.', $key);
        $ref = $this;

        foreach ($parts as $part) {
            if (!isset($ref->$part)) {
                return null;
            }

            $ref = $ref->$part;
        }

        return $ref;
    }

    /**
     * Reads the config settings from the specified $file and returns it
     * @param string $file The file
     * @return array
     */
    public function read(string $file) : array
    {
        return $this->readFilename($this->_app->config_path . '/' . $file);
    }

    /**
     * Reads the config settings from the specified $filename and returns it
     * @param string $filename The filename
     * @return array
     */
    public function readFilename(string $filename) : array
    {
        $app = $this->_app;

        return require($filename);
    }

    /**
     * Writes the specified $data to the specified config $file
     * @param string $file The file
     * @param array $data The data to write
     * @return static
     */
    public function write(string $file, array $data) : static
    {
        $filename = $this->_app->config_path . '/' . basename($file);
        if (!is_writable($filename)) {
            throw new \Exception("The config file '{$file}' is not writable");
        }

        $content = "<?php\n\nreturn " . var_export($data, true) . ";";

        file_put_contents($filename, $content);

        return $this;
    }

    /**
     * Reads the config settings from the specified $file and loads it
     * @param string $file The file
     * @return static
     */
    public function load(string $file) : static
    {
        $this->loadFilename($this->_app->config_path . '/' . $file);

        return $this;
    }

    /**
     * Reads the config settings from the specified $filename and loads it
     * @param string $filename The filename
     * @return static
     */
    public function loadFilename(string $filename) : static
    {
        $settings = $this->readFilename($filename);

        $this->assign($this->getTree($settings));

        return $this;
    }

    /**
     * Loads the config settings from a module's config file
     * @param string $filename The filename
     * @return static
     */
    public function loadModule(string $filename) : static
    {
        $filename = $this->_app->config_path . '/' . $filename;
        if (is_file($filename)) {
            $this->loadFilename($filename);
        }

        return $this;
    }

    /**
     * Normalizes the config options
     */
    protected function normalize()
    {
        if ($this->development->enable) {
            $this->document->css->version = time();
            $this->document->javascript->version = time();
        }

        if ($this->debug->ips) {
            if (in_array($this->_app->ip, $this->debug->ips)) {
                $this->debug->enable = true;
            }
        }
    }

    /**
     * Checks if the proper config options are set
     */
    protected function check()
    {
        if (!$this->url->base) {
            throw new \Exception("The url config option must be set");
        }
    }
}
