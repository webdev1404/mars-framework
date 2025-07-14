<?php
/**
* The Extension's Languages Trait
* @package Venus
*/

namespace Mars\Extensions\Modules\Abilities;

/**
 * The Extension's Languages Trait
 * Trait which allows extensions to load language files
 */
trait LanguagesTrait
{
    /**
     * @var string $lang_key The key used to store the language strings
     */
    //public protected(set) string $lang_key = '';

    /**
     * Loads a file from the extension's languages dir
     * @param string $file The name of the file to load (must not include the .php extension)
     * @return static
     */
    public function loadLanguage(string $file = '') : static
    {
        if (!$file) {
            $file = $this->name;
        }

        //add the language key to the search keys
        $this->app->lang->addSearchKey($this->lang_key);

        if ($this->app->lang->fallback) {
            //check if the fallback language file exists. If it does, load it
            $fallback_filename = $this->getLanguageFilename($this->app->lang->fallback->name, $file);
            if (is_file($fallback_filename)) {
                $this->app->lang->loadFilename($fallback_filename, $this->lang_key);
            }
        }

        if ($this->app->lang->parent) {
            //check if the parent language file exists. If it does, load it
            $parent_filename = $this->getLanguageFilename($this->app->lang->parent->name, $file);
            if (is_file($parent_filename)) {
                $this->app->lang->loadFilename($parent_filename, $this->lang_key);
            }
        }

        $filename = $this->getLanguageFilename($this->app->lang->name, $file);
        if (is_file($filename)) {
            $this->app->lang->loadFilename($filename, $this->lang_key);
        }

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
}
