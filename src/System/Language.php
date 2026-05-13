<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\Drivers;
use Mars\Localization\LocalizationInterface;
use Mars\Extensions\Extension;
use Mars\Extensions\Languages\Language as BaseLanguage;

/**
 * The System's Language Class
 */
class Language extends BaseLanguage
{
    /**
     * @var array $strings The language's strings
     */
    public array $strings = [];

    /**
     * @var array $loaded_files The list of loaded files
     */
    protected array $loaded_files = [];

    /**
     * @var array $lang_files The list of available files found for the language
     */
    protected array $lang_files {
        get {
            if (isset($this->lang_files)) {
                return $this->lang_files;
            }

            $this->lang_files = $this->app->cache->languages->getFiles($this);

            return $this->lang_files;
        }
    }

    /**
     * @var array $extension_files The list of available language files found for extensions
     */
    protected array $extension_files = [];

    /**
     * @var array $extension_types The found types for each extension will be stored here to avoid having to search for them multiple times
     */
    protected array $extension_types = [];

    /**
     * @var string $base_key The key where we're searching for strings without a colon (base keys)
     */
    protected string $base_key = '';

    /**
     * @var string $base_key_old The old base key
     */
    protected string $base_key_old = '';

    /**
     * @var array $drivers_list The supported drivers list
     */
    public protected(set) array $drivers_list = [
        'cookie' => \Mars\Localization\Cookie::class,
        'domain' => \Mars\Localization\Domain::class,
        'path' => \Mars\Localization\Path::class,
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->drivers_list, LocalizationInterface::class, 'localization', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var LocalizationInterface $driver The driver object
     */
    public protected(set) ?LocalizationInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->localization->driver);

            return $this->driver;
        }
    }

    /**
     * @var string $name The name of the language
     */
    public protected(set) string $name {
        get {
            if (isset($this->name)) {
                return $this->name;
            }

            $this->name = $this->codes_list[$this->code] ?? null;
            if (!$this->name) {
                throw new \Exception('No language set in the config file.');
            }

            return $this->name;
        }
    }

    /**
     * @var bool $multi If true, multi-language mode is enabled
     */
    public protected(set) bool $multi {
        get {
            if (isset($this->multi)) {
                return $this->multi;
            }

            $this->multi = false;
            if (count($this->codes_list) > 1) {
                $this->multi = true;
            }

            return $this->multi;
        }
    }

    /**
     * @var array $codes_list The list of available languages for multi-language support
     */
    public protected(set) array $codes_list {
        get {
            if (isset($this->codes_list)) {
                return $this->codes_list;
            }

            $this->codes_list = $this->app->config->language->codes;

            return $this->codes_list;
        }
    }

    /**
     * @var array $codes The list of available language codes, if multi-language is enabled
     */
    public protected(set) array $codes {
        get {
            if (isset($this->codes)) {
                return $this->codes;
            }

            $this->codes = array_keys($this->codes_list);

            return $this->codes;
        }
    }

    /**
     * @var string $code The language's code
     */
    public protected(set) string $code {
        get {
            if (isset($this->code)) {
                return $this->code;
            }

            $this->code = $this->driver->getCode();

            return $this->code;
        }
    }

    /**
     * @var string $default_code The code of the default language, if multi-language is enabled
     */
    public protected(set) string $default_code {
        get {
            if (isset($this->default_code)) {
                return $this->default_code;
            }

            $this->default_code = array_find_key($this->codes_list, fn ($value) => $value === $this->app->config->language->name);
            if (!$this->default_code) {
                throw new \Exception("Default language code not found");
            }

            return $this->default_code;
        }
    }

    /**
     * @var string $url The base URL for the current language
     */
    public protected(set) string $url {
        get {
            if (isset($this->url)) {
                return $this->url;
            }

            $this->url = $this->driver->getUrl($this->code);

            return $this->url;
        }
    }

    /**
     * @var string $request_uri The request URI
     */
    public protected(set) ?string $request_uri {
        get {
            if (isset($this->request_uri)) {
                return $this->request_uri;
            }

            $this->request_uri = $this->driver->getRequestUri();

            return $this->request_uri;
        }
    }

    /**
     * @var bool $can_use_fallback If true, the language can use the fallback language
     */
    public protected(set) bool $can_use_fallback {
        get {
            if (isset($this->can_use_fallback)) {
                return $this->can_use_fallback;
            }

            $this->can_use_fallback = false;
            if ($this->app->config->language->fallback) {
                //if the current language or its parent language is not the fallback language, we can use the fallback.
                if ($this->name !== $this->app->config->language->fallback) {
                    if (!$this->parent || $this->parent->name !== $this->app->config->language->fallback) {
                        $this->can_use_fallback = true;
                    }
                }
            }

            return $this->can_use_fallback;
        }
    }

    /**
     * @var BaseLanguage $fallback The fallback language
     */
    public protected(set) ?BaseLanguage $fallback {
        get {
            if (isset($this->fallback)) {
                return $this->fallback;
            }

            $this->fallback = null;
            if ($this->can_use_fallback) {
                $this->fallback = new BaseLanguage($this->app->config->language->fallback, [], $this->app);
                $this->fallback->boot();
            }

            return $this->fallback;
        }
    }

    /**
     * @var string $parent_name The name of the parent language, if any
     */
    public protected(set) string $parent_name = '';

    /**
     * @var BaseLanguage $parent The parent language, if any
     */
    public protected(set) ?BaseLanguage $parent {
        get {
            if (isset($this->parent)) {
                return $this->parent;
            }

            $this->parent = null;
            if ($this->parent_name) {
                $this->parent = new BaseLanguage($this->parent_name, [], $this->app);
                $this->parent->boot();
            }

            return $this->parent;
        }
    }

    /**
     * Builds the language
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        parent::__construct($this->name, [], $this->app);

        $this->boot();
    }

    /**
     * Boots the language
     */
    public function boot()
    {
        parent::boot();

        if (!$this->parent) {
            return;
        }

        $properties = ['lang', 'datetime_format', 'date_format', 'time_format', 'datetime_picker_format', 'datetime_picker_desc', 'date_picker_format', 'date_picker_desc', 'time_picker_format', 'time_picker_desc', 'decimal_separator', 'thousands_separator'];
        foreach ($properties as $property) {
            if ($this->parent->$property) {
                $this->$property = $this->parent->$property;
            }
        }
    }

    /**
     * Returns a language string
     * @param string $key The key of the language string
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @return string The language string
     */
    public function get(string $key, array $replace = []) : string
    {
        $file = '';
        $index = '';
        $pos = strpos($key, ':');

        if ($pos === false) {
            //no colon in the key. Search for the key in the specified base key
            if ($this->base_key) {
                $file = $this->base_key;
                $index = $key;
            }
        } else {
            //we have a colon in the key. Try to find the file where the key is located
            $file = substr($key, 0, $pos);
            $index = substr($key, $pos + 1);
        }

        if ($file && $index) {
            if (!isset($this->strings[$file])) {
                if (!isset($this->loaded_files[$file])) {
                    $this->loadFile($file);
                }
            }

            $string = $this->strings[$file][$index] ?? $key;
        } else {
            $string = $key;
        }

        if ($replace) {
            $string = str_replace(array_keys($replace), $replace, $string);
        }

        return $string;
    }

    /**
     * Loads the specified file
     * @param string $file The name of the file
     */
    protected function loadFile(string $file)
    {
        $this->loaded_files[$file] = true;

        $filenames = [];
        $parts = explode('.', $file);

        if (count($parts) == 1) {
            //no dots in the file, it's a language file in the languages folder
            $filenames = $this->getFilenames($file);
        } else {
            //we have dots in the file, it's an extension file
            $filenames = $this->getFilenamesForExtension($parts);
        }

        foreach ($filenames as $filename) {
            $this->loadFilename($file, $filename);
        }
    }

    /**
     * Loads the specified filename from anywhere on the disk as a language file
     * @param string $key The key to use for the loaded strings
     * @param string $filename The filename to load
     */
    protected function loadFilename(string $key, string $filename)
    {
        $app = $this->app;
        
        $strings = include($filename);

        if (isset($this->strings[$key])) {
            $this->strings[$key] = array_merge($this->strings[$key], $strings);
        } else {
            $this->strings[$key] = $strings;
        }
    }

    /**
     * Returns the list of filenames for a given file key
     * @param string $file The file key
     * @return array The list of filenames
     */
    protected function getFilenames(string $file) : array
    {
        return $this->lang_files[$file] ?? [];
    }

    /**
     * Returns the list of filenames for a given extension file key
     * @param array $parts The parts of the file key
     * @return array The list of filenames
     */
    protected function getFilenamesForExtension(array $parts) : array
    {
        $type = $parts[0];
        $name = $parts[1];

        if (isset($this->app->extensions[$type])) {
            if (!isset($parts[2])) {
                return [];
            }

            $this->extension_files[$type][$name] ??= $this->getExtensionFilenames($type, $name);

            $file = implode('.', array_slice($parts, 2));

            return $this->extension_files[$type][$name][$file] ?? [];
        } else {
            // search through the modules and themes for the file
            $name = $parts[0];
            $file = implode('.', array_slice($parts, 1));
            $type = $this->getExtensionType($name);

            if (!$type) {
                return [];
            }

            $this->extension_files[$type][$name] ??= $this->getExtensionFilenames($type, $name);

            return $this->extension_files[$type][$name][$file] ?? [];
        }
    }

    /**
     * Returns the list of filenames for a given extension
     * @param string $type The type of the extension
     * @param string $name The name of the extension
     * @param bool $check_enabled If true, will check if the extension is enabled
     * @return array The list of filenames
     */
    protected function getExtensionFilenames(string $type, string $name, bool $check_enabled = true) : array
    {
        $manager = $this->app->extensions[$type]();
        if ($check_enabled && !$manager->isEnabled($name)) {
            return [];
        }
        
        return $this->app->cache->languages->getExtensionFiles($this, $manager->get($name));
    }

    /**
     * Returns the type of a given extension name, by searching through the enabled extensions
     * @param string $name The name of the extension
     * @return string The type of the extension
     */
    protected function getExtensionType(string $name) : string
    {
        if (isset($this->extension_types[$name])) {
            return $this->extension_types[$name];
        }

        $type = '';
        foreach ($this->app->extensions as $extension_type => $callback) {
            $manager = $callback();
            if ($manager->isEnabled($name)) {
                $type = $extension_type;
                break;
            }
        }

        $this->extension_types[$name] = $type;

        return $type;
    }

    /**
     * Returns the filename of a template in the language's templates
     * @param string $template The name of the template
     * @return string|null The full path to the template, or null if not found
     */
    public function getTemplateFilename(string $template) : ?string
    {
        if (isset($this->templates[$template])) {
            return $this->templates_path . '/' . $template;
        }

        if ($this->parent) {
            if (isset($this->parent->templates[$template])) {
                return $this->parent->templates_path . '/' . $template;
            }
        }

        if ($this->fallback) {
            if (isset($this->fallback->templates[$template])) {
                return $this->fallback->templates_path . '/' . $template;
            }
        }

        return null;
    }

    /**
     * Adds a base key to the list of keys where we're searching for strings
     * @param string $key The key(s) to add
     * @return static
     */
    public function setBaseKey(string $key) : static
    {
        $this->base_key_old = $this->base_key;
        $this->base_key = $key;

        return $this;
    }

    /**
     * Restores the base key to the previous one
     * @return static
     */
    public function restoreBaseKey() : static
    {
        //unset the loaded strings for the current base key, to save memory
        unset($this->strings[$this->base_key]);
        unset($this->loaded_files[$this->base_key]);

        $this->base_key = $this->base_key_old;

        return $this;
    }
}
