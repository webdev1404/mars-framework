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
    use BaseLanguagesTrait {
        loadLanguage as protected baseLoadLanguage;
    }

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

        $this->baseLoadLanguage($file, $this->lang_key);

        //load from the language's files folder
        $filename_rel = $this->path_rel . '/' . $file;
        $this->app->lang->loadFile($filename_rel, $this->lang_key);

        return $this;
    }
}
