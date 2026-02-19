<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\Drivers;
use Mars\Localization\LocalizationInterface;
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
     * @var array $local_keys The keys where we're search for strings without a dot (local keys)
     */
    protected array $local_keys = [];

    /**
     * @var array $local_keys_old The old local keys
     */
    protected array $local_keys_old = [];

    /**
     * @var array $registered_keys The list of registered keys
     */
    protected array $registered_keys = [];

    /**
     * @var array $loaded_keys The list of loaded files
     */
    protected array $loaded_keys = [];

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
                //if the current language or it's parent language is not the fallback language, we can use the fallback.
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
     * Registers a language file to be loaded when the key is requested
     * @param string $key The key of the language file
     * @param string|array $filename The filename of the language file(s)
     * @return static
     */
    public function register(string $key, string|array $filename) : static
    {
        $filenames = (array)$filename;

        if (isset($this->registered_keys[$key])) {
            $this->registered_keys[$key] = array_merge($this->registered_keys[$key], $filenames);
        } else {
            $this->registered_keys[$key] = $filenames;
        }

        return $this;
    }

    /**
     * Registers a language file, from the language's files folder, to be loaded when the key is requested
     * @param string $key The key of the language file
     * @param string $file The file to register
     * @return static
     */
    public function registerFile(string $key, string $file) : static
    {
        $filenames = $this->findFilenames($file);
        if (!$filenames) {
            return $this;
        }

        return $this->register($key, $filenames);
    }

    /**
     * Loads a language file
     * @param string $key The key of the language file
     */
    protected function load(string $key)
    {
        $this->loaded_keys[$key] = true;

        if (isset($this->registered_keys[$key])) {
            foreach ($this->registered_keys[$key] as $filename) {
                $this->loadFilename($key, $filename);
            }
        } else {
            $this->loadFile($key);
        }
    }

    /**
     * Loads the specified $file from the languages folder
     * @param string $file The name of the file to load (must not include the .php extension)
     */
    protected function loadFile(string $file)
    {
        $key = $file;

        $filenames = $this->findFilenames($file);
        foreach ($filenames as $filename) {
            $this->loadFilename($key, $filename);
        }
    }

    /**
     * Loads the specified filename from anywhere on the disk as a language file
     * @param string $key The key to use for the loaded strings
     * @param string $filename The filename to load
     * @return static
     */
    public function loadFilename(string $key, string $filename) : static
    {
        $app = $this->app;
        
        $strings = include($filename);

        if (isset($this->strings[$key])) {
            $this->strings[$key] = array_merge($this->strings[$key], $strings);
        } else {
            $this->strings[$key] = $strings;
        }

        return $this;
    }

    /**
     * Finds the filenames for a language file, including parent and fallback languages
     * @param string $file The file to find
     * @return array The list of filenames
     */
    protected function findFilenames(string $file) : array
    {
        $filenames = [];
        $file = $file . '.php';

        if ($this->fallback) {
            //check if the fallback language file exists
            if (isset($this->fallback->files[$file])) {
                $filenames[] = $this->fallback->files_path . '/' . $file;
            }
        }

        if ($this->parent) {
            //check if the parent language file exists. If it does, load it
            if (isset($this->parent->files[$file])) {
                $filenames[] = $this->parent->files_path . '/' . $file;
            }
        }

        if (isset($this->files[$file])) {
            $filenames[] = $this->files_path . '/' . $file;
        }

        return $filenames;
    }

    /**
     * Unloads the specified key
     * @param string $key The key to unload
     * @return static
     */
    public function unload(string $key) : static
    {
        if (isset($this->strings[$key])) {
            unset($this->strings[$key]);
        }

        if (isset($this->registered_keys[$key])) {
            unset($this->registered_keys[$key]);
        }

        return $this;
    }

    /**
     * Returns a language string
     * @param string|array $string The string as defined in the languages file. If an array is passed, the first found string will be returned
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @return string The language string
     */
    public function get(string|array $string, array $replace = []) : string
    {
        $strings = (array)$string;
        $found_string = '';

        foreach ($strings as $string) {
            $index = strpos($string, '.');
    
            if ($index === false) {
                //no dot in the key. Search for the string in the specified local keys
                foreach ($this->local_keys as $key) {
                    if (!isset($this->strings[$key])) {
                        $this->load($key);
                    }

                    $found_string = $this->strings[$key][$string] ?? '';
                    if ($found_string) {
                        break;
                    }
                }
            } else {
                //we have a dot in the key. Search for the string in the specified file
                $key = substr($string, 0, $index);
                $string_key = substr($string, $index + 1);

                if (!isset($this->strings[$key])) {
                    if (!isset($this->loaded_keys[$key])) {
                        $this->load($key);
                    }
                }

                $found_string = $this->strings[$key][$string_key] ?? '';
            }

            if ($found_string) {
                break;
            }
        }

        //set the first string as found string, if none was found
        if (!$found_string) {
            $found_string = array_first($strings);
        }

        if ($replace) {
            $found_string = str_replace(array_keys($replace), $replace, $found_string);
        }

        return $found_string;
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
     * Adds a local key to the list of keys where we're searching for strings
     * @param string $key The key to add
     * @return static
     */
    public function addLocalKey(string $key) : static
    {
        array_unshift($this->local_keys, $key);

        return $this;
    }

    /**
     * Saves the current local keys to the old ones
     * @return static
     */
    public function saveLocalKeys() : static
    {
        $this->local_keys_old = $this->local_keys;

        return $this;
    }

    /**
     * Restores the local keys to the previous ones
     * @return static
     */
    public function restoreLocalKeys() : static
    {
        $this->local_keys = $this->local_keys_old;

        return $this;
    }
}
