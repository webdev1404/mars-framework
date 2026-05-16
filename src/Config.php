<?php
/**
* The Config Class
* @package Mars
*/

namespace Mars;

use Mars\App\Info;
use Mars\App\HiddenProperty;
use Mars\Config\Container;
use Mars\Config\Defaults;
use Mars\Config\ArrayResult;
use Mars\Extensions\Extension;

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
     */
    protected function init()
    {
        $cache_filename = $this->_app->cache_path . '/config/config-all.php';

        if (is_file($cache_filename)) {
            $this->readFromCache($cache_filename);
            return;
        }
        
        $settings = $this->readAllFiles();

        $this->assign($this->getTree($settings));

        $this->writeToCache($cache_filename);
    }

    /**
     * Reads the config settings from all the config files
     * @return array The config settings
     */
    protected function readAllFiles() : array
    {
        $settings = $this->readFilename($this->_app->framework_path . '/config/default.php');
        
        $files = glob($this->_app->config_path . '/*.php');
        foreach ($files as $file) {
            $settings = array_merge($settings, $this->read(basename($file)));
        }

        return $settings;
    }

    /**
     * Loads the config settings from cache
     * @param string $filename The cache filename
     */
    protected function readFromCache(string $filename)
    {
        $settings = $this->readFilename($filename);

        foreach ($settings as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Caches the config settings to a file
     * @param string $filename The cache filename
     */
    protected function writeToCache(string $filename)
    {
        if ($this->development->enable) {
            return;
        }
        
        $properties = $this->_app->object->getProperties($this);

        $content = "<?php\n\nreturn " . var_export($properties, true) . ";";

        file_put_contents($filename, $content);
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
     * Reads the config settings from the specified file, found in the config directory
     * @param string $file The file
     * @return array The config settings
     */
    public function read(string $file) : array
    {
        return $this->readFilename($this->_app->config_path . '/' . $file);
    }

    /**
     * Reads the config settings from the specified filename
     * @param string $filename The filename
     * @return array The config settings
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
     * Normalizes the config options
     */
    protected function normalize()
    {
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
        if (!$this->url->base && !defined('MARS_SETUP')) {
            throw new \Exception("The 'url.base' config option must be set in file 'config.php'. Either set it manually or run the setup script to set it automatically.");
        }
    }

    /**
     * Magic method to get extension config options as properties. Eg: $config->users->registration->open
     * @param string $name The name of the property
     * @return mixed The config value or null if not found
     */
    public function __get($name)
    {
        //we have an undefined property, try to find it in the extensions config dir
        $settings = $this->getExtensionSettings($name);
        if ($settings) {
            $this->assign($this->getTree($settings));

            return $this->$name;
        }

        return null;
    }

    /**
     * Gets the config settings for the specified extension name
     * @param string $name The name of the extension
     * @return array The config settings
     */
    protected function getExtensionSettings(string $name) : array
    {
        $settings = $this->_app->cache->config->get($name);
        if ($this->_app->development) {
            $settings = null;
        }

        if ($settings !== null) {
            return $settings;
        }

        $settings = $this->readSettingsForExtension($name);

        $this->_app->cache->config->set($name, $settings);

        return $settings;
    }

    /**
     * Returns the settings for the specified extension by reading the config files from the extension's config directory and the app's config directory
     * @param string $name The name of the extension
     * @return array The config settings
     */
    protected function readSettingsForExtension(string $name) : array
    {
        $extension = $this->findExtension($name);
        if (!$extension) {
            return [];
        }

        $name = App::toCamelCase($extension->name);

        $extension_settings = $this->readSettingsForExtensionFromDir($extension->config_path, $name);

        $config_settings = $this->readSettingsForExtensionFromDir($this->_app->config_path . '/' . $extension->path_rel, $name);

        return array_merge($extension_settings, $config_settings);
    }
    
    /**
     * Finds the extension with the specified name. It will search for the actual name and the kebab case name. Eg: 'myExtension' and 'my-extension'
     * @param string $name The name of the extension
     * @return Extension|null The extension object or null if not found
     */
    protected function findExtension(string $name) : ?Extension
    {
        $extension = $this->_app->extensions->get($name);
        if ($extension) {
            return $extension;
        }

        return $this->_app->extensions->get(App::toKebabCase($name));
    }

    /**
     * Reads the config settings from the specified directory and returns them as an array
     * @param string $path The path to read the config settings from
     * @param string $extension_name The name of the extension, used as a prefix for the config keys
     * @return array The config settings
     */
    protected function readSettingsForExtensionFromDir(string $path, string $extension_name) : array
    {
        if (!is_dir($path)) {
            return [];
        }

        $settings = [];
        $files = $this->_app->dir->get($path, false, true, ['php']);

        foreach ($files as $file) {
            $name = $this->_app->file->getStem($file);

            $file_settings = $this->readFilename($file);
            $file_settings_keys = array_map(function ($val) use ($extension_name, $name) {
                return $extension_name . '.' . $name . '.' . $val;
            }, array_keys($file_settings));

            $settings = array_merge($settings, array_combine($file_settings_keys, $file_settings));
        }

        return $settings;
    }
}
