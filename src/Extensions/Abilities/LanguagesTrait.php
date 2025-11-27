<?php
/**
* The Extension's Languages Trait
* @package Mars
*/

namespace Mars\Extensions\Abilities;

/**
 * The Extension's Languages Trait
 * Trait which allows extensions to load language files
 */
trait LanguagesTrait
{
    /**
     * @var array $language_files The list of language files in the extension
     */
    public protected(set) array $language_files {
        get {
            if (isset($this->language_files)) {
                return $this->language_files;
            }

            $this->language_files = $this->getCachedFiles($this->path . '/' . static::DIRS['languages']);

            return $this->language_files;
        }
    }

    /**
     * Loads a file from the extension's languages dir
     * @param string $file The name of the file to load (must not include the .php extension)
     * @return static
     */
    public function loadLanguage(string $file, string $key) : static
    {
        if ($this->app->lang->fallback) {
            //check if the fallback language file exists. If it does, load it
            if ($this->hasLanguageFile($this->app->lang->fallback->name, $file)) {
                $this->app->lang->loadFilename($this->getLanguageFilename($this->app->lang->fallback->name, $file), $key);
            }
        }

        if ($this->app->lang->parent) {
            //check if the parent language file exists. If it does, load it
            if ($this->hasLanguageFile($this->app->lang->parent->name, $file)) {
                $this->app->lang->loadFilename($this->getLanguageFilename($this->app->lang->parent->name, $file), $key);
            }
        }

        if ($this->hasLanguageFile($this->app->lang->name, $file)) {
            $this->app->lang->loadFilename($this->getLanguageFilename($this->app->lang->name, $file), $key);
        }

        return $this;
    }

    /**
     * Registers a language file for the extension
     * @param string $name The name of the language
     * @param string $file The file to register
     * @return static
     */
    public function registerLanguage(string $name, string $file) : static
    {
        $this->app->lang->register($name, function() use ($name, $file) {
            $this->loadLanguage($file, $name);
        });

        return $this;
    }

    /**
     * Returns the filename of a language file
     * @param string $language_name The name of the language
     * @param string $file The name of the file to load
     * @return string The filename of the language file
     */
    public function getLanguageFilename(string $language_name, string $file) : string
    {
        return $this->path . '/' . static::DIRS['languages'] . '/' . $language_name . '/' . $file . '.php';
    }

    /**
     * Checks if the specified language file exists
     * @param string $language_name The name of the language
     * @param string $file The name of the file to check
     * @return bool True if the file exists, false otherwise
     */
    public function hasLanguageFile(string $language_name, string $file) : bool
    {
        $file = $language_name . '/' . $file . '.php';

        return isset($this->language_files[$file]);
    }
}
