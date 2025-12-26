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
     * Registers a language file for the extension
     * @param string $key The key of the language
     * @param string $file The filename (without extension) to register
     * @return static
     */
    public function registerLanguageFile(string $key, string $file) : static
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
