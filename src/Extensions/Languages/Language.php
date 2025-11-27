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
     * @var array $strings The language's strings
     */
    public array $strings = [];

    /**
     * @var array $strings_search The keys where we're search for strings without a dot
     */
    protected array $strings_search = [];

    /**
     * @var array $strings_search_old The old search keys
     */
    protected array $strings_search_old = [];

    /**
     * @var array $loaded_files The list of loaded files
     */
    protected array $loaded_files = [];

    /**
     * @var array $invalid_files The list of invalid files we tried to load, but couldn't
     */
    protected array $invalid_files = [];

    /**
     * @var array $registered_files The list of registered files
     */
    protected array $registered_files = [];

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
    public protected(set) array $files {
        get {
            if (isset($this->files)) {
                return $this->files;
            }

            $this->files = $this->getCachedFiles($this->files_path);

            return $this->files;
        }
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
    public protected(set) array $templates {
        get {
            if (isset($this->templates)) {
                return $this->templates;
            }

            $this->templates = $this->getCachedFiles($this->templates_path);

            return $this->templates;
        }
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
     * Checks if the specified file exists in the language's files
     * @param string $file The filename to check
     * @return bool True if the file exists, false otherwise
     */
    public function isFile(string $file) : bool
    {
        return isset($this->files[$file . '.php']);
    }

    /**
     * Checks if the specified template exists in the language's templates
     * @param string $file The filename to check
     * @return bool True if the file exists, false otherwise
     */
    public function isTemplate(string $file) : bool
    {
        return isset($this->templates[$file]);
    }

    /**
     * Loads the specified $file from the languages folder
     * @param string $file The name of the file to load (must not include the .php extension)
     * @param string|null $key The key to use for the loaded strings
     * @return static
     */
    public function loadFile(string $file, ?string $key = null) : static
    {
        if (isset($this->loaded_files[$file])) {
            return $this;
        }
        if (isset($this->invalid_files[$file])) {
            return $this;
        }

        if (!$this->isFile($file)) {
            $this->invalid_files[] = $file;

            return $this;
        }

        $key ??= $file;
        $this->loaded_files[$file] = true;

        $this->loadFilename($this->getFilename($file), $key);

        return $this;
    }

    /**
     * Returns the full path to the specified language file
     * @param string $file The filename to get the path for
     * @return string The full path to the file
     */
    public function getFilename(string $file) : string
    {
        return $this->files_path . '/' . $file . '.php';
    }

    /**
     * Loads the specified filename from anywhere on the disk as a language file
     * @param string $filename The filename to load
     * @param string $key The key to use for the loaded strings
     * @return static
     */
    public function loadFilename(string $filename, string $key) : static
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
     * Returns a language string
     * @param string $string The string as defined in the languages file
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @param string $key The key to use for the loaded strings
     * @return string The language string
     */
    public function get(string $string, array $replace = [], $key = '') : string
    {
        $keys = [];
        $index = strpos($string, '.');
        
        if ($index !== false) {
            //we have a dot in the key. Search for the string in the specified file
            $file = substr($string, 0, $index);
            $string = substr($string, $index + 1);
            $keys = [$file];

            $this->load($file);
        } else {
            $keys = $key ? [$key] : $this->strings_search;
        }

        // See if the string exists in the specified keys
        foreach ($keys as $key) {
            if (isset($this->strings[$key][$string])) {
                $string = $this->strings[$key][$string];
                break;
            }
        }

        if ($replace) {
            $string = str_replace(array_keys($replace), $replace, $string);
        }

        return $string;
    }

    /**
     * Loads a language file
     * @param string $file The name of the file to load
     */
    protected function load(string $file) 
    {
        if (!isset($this->registered_files[$file])) {
            //the file isn't registered, so load it normally
            $this->loadFile($file);
            return;
        } else {
            //execute the registered callback
            $this->registered_files[$file]();
        }
    }

    /**
     * Adds a search key to the list of keys where we're searching for strings
     * @param string $key The key to add
     * @return static
     */
    public function addSearchKey(string $key) : static
    {
        array_unshift($this->strings_search, $key);

        return $this;
    }

    /**
     * Saves the current prefixes to the old ones
     * @return static
     */
    public function saveSearchKeys() : static
    {
        $this->strings_search_old = $this->strings_search;

        return $this;
    }

    /**
     * Restores the prefixes to the previous ones
     * @return static
     */
    public function restoreSearchKeys() : static
    {
        $this->strings_search = $this->strings_search_old;

        return $this;
    }

    /**
     * Registers a callback to be called to load a language file
     * @param string $name The name of the language file
     * @param callable $callback The callback to be called to load the file
     * @return static
     */
    public function register(string $name, callable $callback) : static
    {
        $this->registered_files[$name] = $callback;

        return $this;
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
}
