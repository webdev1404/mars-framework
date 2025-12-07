<?php
/**
* The Language Class
* @package Mars
*/

namespace Mars\Extensions\Languages;

use Mars\App;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Abilities\FilesCacheTrait;

/**
 * The Language Class
 */
class Language extends Extension
{
    use FilesCacheTrait;
    
    /**
     * @internal
     */
    public const array DIRS = [
        'assets' => 'assets',
        'files' => 'files',
        'templates' => 'templates',
        'setup' => 'setup',
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['files', 'templates'];

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
     * @var string $files_path The path for the theme's files folder
     */
    public protected(set) string $files_path {
        get {
            if (isset($this->files_path)) {
                return $this->files_path;
            }

            $this->files_path = $this->path . '/' . static::DIRS['files'];

            return $this->files_path;
        }
    }

    /**
     * @var array $files Array with the list of available files
     */
    public array $files {
         get => $this->files_cache_list['files'] ?? [];
    }

    /**
     * @var string $templates_path The path for the languages's templates folder
     */
    public protected(set) string $templates_path {
        get {
            if (isset($this->templates_path)) {
                return $this->templates_path;
            }

            $this->templates_path = $this->path . '/' . static::DIRS['templates'];

            return $this->templates_path;
        }
    }

    /**
     * @var array $templates Array with the list of available templates
     */
    public array $templates {
        get => $this->files_cache_list['templates'] ?? [];
    }

    /**
     * @var string $encoding The encoding of the language
     */
    public string $encoding = 'UTF-8';

    /**
     * @var string $lang The language's html lang attribute
     */
    public string $lang = '';

    /**
     * @var string $datetime_format The format in which a timestamp will be displayed
     */
    public string $datetime_format = 'm/d/Y h:i:s a';

    /**
     * @var string $date_format The format in which a date will be displayed
     */
    public string $date_format = 'm/d/Y';

    /**
     * @var string $time_format The format in which the time of the day will be displayed
     */
    public string $time_format = 'h:i:s a';

    /**
     * @var string datetime_picker_format The format of the datetime picker
     */
    public string $datetime_picker_format = 'm-d-Y H:i:s';

    /**
     * @var string datetime_picker_desc The description of the datetime picker
     */
    public string $datetime_picker_desc = 'mm-dd-yyyy hh:mm:ss';

    /**
     * @var string date_picker_format The format of the date picker
     */
    public string $date_picker_format = 'm-d-Y';

    /**
     * @var string date_picker_desc The description of the date picker
     */
    public string $date_picker_desc = 'mm-dd-yyyy';

    /**
     * @var string time_picker_format The format of the time picker
     */
    public string $time_picker_format = 'H:i:s';

    /**
     * @var string time_picker_desc The description of the time picker
     */
    public string $time_picker_desc = 'hh:mm:ss';

    /**
     * @var string $decimal_separator The language's decimal_separator
     */
    public string $decimal_separator = '.';

    /**
     * @var string $thousands_separator The language's thousands_separator
     */
    public string $thousands_separator = ',';

    /**
     * @internal
     */
    protected static string $manager_class = Languages::class;

    /**
     * @internal
     */
    protected static ?Extensions $manager_instance = null;

    /**
     * @internal
     */
    protected static string $type = 'language';

    /**
     * @internal
     */
    protected static string $base_dir = 'languages';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Languages";

    /**
     * Builds the language
     * @param string $name The name of the exension
     * @param array $params The params passed to the language, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        parent::__construct($name, $params, $app);

        $this->init();
    }

    /**
     * Registers a language file to be loaded when the key is requested
     * @param string $key The key of the language file
     * @param string|array $filename The filename of the language file(s)
     * @return static
     */
    public function register(string $key, string|array $filename) : static
    {
        $filenames = $this->app->array->get($filename);

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
        $file = $file . '.php';

        if (!isset($this->files[$file])) {
            return $this;
        }

        return $this->register($key, $this->files_path . '/' . $file);
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
        $file = $file . '.php';

        if (!isset($this->files[$file])) {
            return;
        }

        $this->loadFilename($key, $this->files_path . '/' . $file);
    }

    /**
     * Loads the specified filename from anywhere on the disk as a language file
     * @param string $key The key to use for the loaded strings
     * @param string $filename The filename to load
     * @return static
     */
    public function loadFilename(string $key, string $filename) : static
    {
        $strings = include($filename);

        if (isset($this->strings[$key])) {
            $this->strings[$key] = array_merge($this->strings[$key], $strings);
        } else {
            $this->strings[$key] = $strings;
        }

        return $this;
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
     * @param string $string The string as defined in the languages file
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @param string $key The key to use for the loaded strings
     * @return string The language string
     */
    public function get(string $string, array $replace = [], $key = '') : string
    {
        $keys = [];
        $found_string = '';
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
        if (!isset($this->templates[$template])) {
            return null;
        }

        return $this->templates_path . '/' . $template;
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
