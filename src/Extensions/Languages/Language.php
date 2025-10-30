<?php
/**
* The Language Class
* @package Mars
*/

namespace Mars\Extensions\Languages;

use Mars\App;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;

/**
 * The Language Class
 */
class Language extends Extension
{
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

            $filename = "language-{$this->name}-files";

            $this->files = $this->getExistingFiles($this->files_path, $filename);

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

            $filename = "language-{$this->name}-templates";

            $this->templates = $this->getExistingFiles($this->templates_path, $filename);

            return $this->templates;
        }
    }

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

            $this->loadFile($file);
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
