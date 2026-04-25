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
     * @var string $languages_path The path to the extension's languages dir
     */
    public protected(set) string $languages_path {
        get {
            if (isset($this->languages_path)) {
                return $this->languages_path;
            }

            $this->languages_path = $this->path . '/' . static::DIRS['languages'];

            return $this->languages_path;
        }
    }

    /**
     * @var array $language_files The list of language files in the extension
     */
    public protected(set) array $language_files {
        get {
            if (isset($this->language_files)) {
                return $this->language_files;
            }

            $this->language_files = $this->files_cache_list[static::DIRS['languages']] ?? [];

            return $this->language_files;
        }
    }

    /**
     * @var array $language_keys The loaded language keys
     */
    protected array $language_keys = [];

    /**
     * Loads a file from the extension's languages dir
     * @param string $file The name of the file to load (must not include the .php extension)
     * @return static
     */
    public function loadLanguage(string $file, ?string $key = null) : static
    {
        //if a key is provided, just register the language file without adding it to the local keys list or allowing it to be overridden
        if ($key) {
            $this->registerLanguage($file, $key);

            return $this;
        }

        $key = static::getType() . '.' . $this->name . '.' . $file;

        //add the language key to the local keys list
        $this->app->lang->addLocalKey($key);

        $this->registerLanguage($file, $key);

        //allow the language file to be overridden in the lang's files folder
        $this->app->lang->registerFile($this->path_rel . '/' . $file, $key);

        $this->language_keys[] = $key;

        return $this;
    }

    /**
     * Unloads the loaded language files
     */
    protected function unloadLanguages()
    {
        foreach ($this->language_keys as $key) {
            $this->app->lang->unload($key);
        }

        $this->language_keys = [];
    }

    /**
     * Registers a language file for the extension
     * @param string $file The filename (without extension) to register
     * @param string $key The key of the language
     * @return static
     */
    public function registerLanguage(string $file, string $key) : static
    {
        $filenames = [];
        $file = $file . '.php';

        //check if we have the default language file
        $lang_file = $file;
        if (isset($this->language_files[$lang_file])) {
            $filenames[] = $this->languages_path . '/' . $lang_file;
        }

        if ($this->app->lang->fallback) {
            //check if the fallback language file exists
            $lang_file = $this->app->lang->fallback->name . '/' . $file;
            if (isset($this->language_files[$lang_file])) {
                $filenames[] = $this->languages_path . '/' . $lang_file;
            }
        }

        if ($this->app->lang->parent) {
            //check if the parent language file exists. If it does, load it
            $lang_file = $this->app->lang->parent->name . '/' . $file;
            if (isset($this->language_files[$lang_file])) {
                $filenames[] = $this->languages_path . '/' . $lang_file;
            }
        }

        //check if we have the language file for the current language
        $lang_file = $this->app->lang->name . '/' . $file;
        if (isset($this->language_files[$lang_file])) {
            $filenames[] = $this->languages_path . '/' . $lang_file;
        }

        $this->app->lang->register($key, $filenames);

        return $this;
    }
}
