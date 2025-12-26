<?php
/**
* The Extension's Languages Trait
* @package Mars
*/

namespace Mars\Extensions\Modules\Abilities;

use Mars\Extensions\Abilities\LanguagesTrait as BaseLanguagesTrait;

/**
 * The Extension's Languages Trait
 * Trait which allows extensions to load language files
 */
trait LanguagesTrait
{
    use BaseLanguagesTrait;

    /**
     * @var array $loaded_language_keys The loaded language keys
     */
    protected array $loaded_language_keys = [];

    /**
     * Loads a file from the extension's languages dir
     * @param string $file The name of the file to load (must not include the .php extension)
     * @return static
     */
    public function loadLanguage(string $file) : static
    {
        $key = 'module.' . $this->name . '.' . $file;

        //add the language key to the local keys list
        $this->app->lang->addLocalKey($key);

        $this->registerLanguageFile($key, $file);

        //allow the language file to be overridden in the lang's files folder
        $this->app->lang->registerFile($key, $this->path_rel . '/' . $file);

        $this->loaded_language_keys[] = $key;

        return $this;
    }

    /**
     * Unloads the loaded language files
     */
    protected function unloadLanguages()
    {
        foreach ($this->loaded_language_keys as $key) {
            $this->app->lang->unload($key);
        }

        $this->loaded_language_keys = [];
    }
}
