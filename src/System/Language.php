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
use Mars\Extensions\Language as BaseLanguage;

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
     * @var array $files The list of available files found for the language
     */
    protected array $files {
        get {
            if (isset($this->files)) {
                return $this->files;
            }

            $this->files = $this->getFiles();

            return $this->files;
        }
    }

    /**
     * @var array $contexts The list of contexts for the language
     */
    protected array $contexts = [];

    /**
     * @var array $extension_names The list of found extensions names
     */
    protected array $extension_names = [];

    /**
     * @var array $extension_files The list of available language files found for extensions
     */
    protected array $extension_files = [];

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

            $this->url = $this->getUrlByCode($this->code);

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
     * Returns the URL for the given language code
     * @param string $code The language code
     * @return string The URL for the given language code
     */
    public function getUrlByCode(string $code) : string
    {
        static $urls = [];
        if (isset($urls[$code])) {
            return $urls[$code];
        }

        $urls[$code] = $this->driver->getUrl($code);

        return $urls[$code];
    }

    /**
     * Returns a language string
     * @param string $key The key of the language string
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @return string The language string
     */
    public function get(string $key, array $replace = []) : string
    {
        $colon_pos = strpos($key, ':');

        $extension = '';
        if ($colon_pos !== false) {
            $extension = substr($key, 0, $colon_pos);

            $this->extension_names[$extension] ??= $this->getExtensionName($extension);
            $extension = $this->extension_names[$extension];

            $key = substr($key, $colon_pos + 1);
        }

        $dot_pos = strpos($key, '.');
        if ($dot_pos !== false) {
            $file = substr($key, 0, $dot_pos);
            $index = substr($key, $dot_pos + 1);

            //try to locate the extension file in the contexts, if any
            if (!$extension && $this->contexts) {
                $keys = array_reverse(array_keys($this->contexts));

                foreach ($keys as $context) {
                    if (isset($this->contexts[$context][$file . '.php'])) {
                        $extension = $context;
                        break;
                    }
                }
            }

            $string_key = $extension ? $extension . ':' . $file : $file;
            if (!isset($this->strings[$string_key])) {
                $this->loadFile($extension, $file, $string_key);
            }

            $string = $this->strings[$string_key][$index] ?? $key;
        } else {
            //if we have no dot in the key, simply return the key as the string
            $string = $key;
        }

        if ($replace) {
            $string = str_replace(array_keys($replace), $replace, $string);
        }

        return $string;
    }

    /**
     * Loads the specified file
     * @param string $extension The name of the extension, if any
     * @param string $file The name of the file
     */
    protected function loadFile(string $extension, string $file, string $key)
    {
        if ($extension) {
            $filenames = $this->getExtensionFilenames($extension, $file);
        } else {
            $filenames = $this->getFilenames($file);
        }

        foreach ($filenames as $filename) {
            $this->loadFilename($key, $filename);
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
        return $this->files[$file] ?? [];
    }

    /**
     * Returns the language's files list
     * @return array The list of files
     */
    protected function getFiles() : array
    {
        $cache_name = $this->name . '-files';

        $files = $this->cache->get($cache_name);
        if ($this->development) {
            $files = null;
        }

        if ($files !== null) {
            return $files;
        }

        $files = [];
        if ($this->parent) {
            //add the parent language files
            $this->readFiles($files, $this->parent->files_path);
        }

        //add the language files
        $this->readFiles($files, $this->files_path);

        $this->cache->set($cache_name, $files);

        return $files;
    }

    /**
     * Adds a list of files from a given directory to the provided files array
     * @param array $files The array to add the files to
     * @param string $dir The directory to get the files from
     */
    protected function readFiles(array &$files, string $dir)
    {
        $files_list = $this->app->dir->get($dir, false, true, ['php']);

        foreach ($files_list as $file) {
            $name = $this->app->file->getStem($file);

            if (!isset($files[$name])) {
                $files[$name] = [$file];
            } else {
                $files[$name][] = $file;
            }
        }
    }

    /**
     * Returns the extension name
     * @param string $name The name of the extension
     * @return string The name of the extension
     */
    protected function getExtensionName(string $name) : string
    {
        $parts = explode('.', $name);

        if (count($parts) == 1) {
            $name = $parts[0];
            $type = $this->app->extensions->getType($name, 'languages');
        } else {
            [$type, $name] = $parts;
        }

        return $type . '.' . $name;
    }

    /**
     * Returns the list of filenames for a given extension
     * @param string $extension The name of the extension
     * @param string $file The name of the file
     * @return array The list of filenames
     */
    protected function getExtensionFilenames(string $extension, string $file) : array
    {
        [$type, $name] = explode('.', $extension);

        if (!isset($this->app->extensions->list[$type])) {
            //invalid extension type, return empty array
            return [];
        }

        if (!isset($this->extension_files[$type][$name])) {
            $cache_name = $type . '-' . $name . '-' . $this->name . '-language-files';

            $filenames = $this->cache->get($cache_name);
            if ($this->development) {
                $filenames = null;
            }

            if ($filenames === null) {
                $filenames = $this->readFilenamesForExtension($type, $name);

                $this->cache->set($cache_name, $filenames);
            }

            $this->extension_files[$type][$name] = $filenames;
        }

        return $this->extension_files[$type][$name][$file] ?? [];
    }

    /**
     * Returns the list of filenames for a given extension name and type by searching for the files in the extension's languages folder and in the language's files folder
     * @param string $type The type of the extension
     * @param string $name The name of the extension
     * @return array The list of filenames
     */
    protected function readFilenamesForExtension(string $type, string $name) : array
    {
        $extension = $this->app->extensions->get($name, $type, 'languages');
        if (!$extension) {
            return [];
        }

        if (!is_dir($extension->languages_path)) {
            return [];
        }

        $filenames = [];
        $files = $this->app->dir->get($extension->languages_path, false, false, ['php']);
        foreach ($files as $file) {
            $name = $this->app->file->getStem($file);

            $filenames[$name] = $this->findFilenamesForExtension($extension, $file);
        }

        return $filenames;
    }

    /**
     * Finds the list of filenames which exist for a given extension and file
     * @param Extension $extension The extension to find the filenames for
     * @param string $file The file key
     * @return array The list of filenames
     */
    protected function findFilenamesForExtension(Extension $extension, string $file) : array
    {
        $filenames = [];

        //do we have the default file?
        $filenames[] = $extension->languages_path . '/' . $file;

        //do we have a file for the parent language?
        if ($this->parent) {
            $filenames[] = $extension->languages_path . '/' . $this->parent->name . '/' . $file;
        }

        //do we have a file for the language?
        $filenames[] = $extension->languages_path . '/' . $this->name . '/' . $file;

        //check if the extension has the file in its languages folder
        $path_rel = $extension->path_rel . '/' . $file;

        if ($this->parent) {
            $filenames[] = $this->parent->files_path . '/' . $path_rel;
        }

        $filenames[] = $this->files_path . '/' . $path_rel;

        $filenames = array_filter($filenames, function ($filename) {
            return is_file($filename);
        });

        return $filenames;
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

        return null;
    }

    /**
     * Adds context files
     * @param string $name The name of the context
     * @param array $files The list of files assigned to the context
     * @return static
     */
    public function addContext(string $name, array $files) : static
    {
        $this->contexts[$name] = $files;

        return $this;
    }

    /**
     * Removes a context
     * @param string $name The name of the context
     * @return static
     */
    public function removeContext(string $name) : static
    {
        unset($this->contexts[$name]);

        return $this;
    }
}
