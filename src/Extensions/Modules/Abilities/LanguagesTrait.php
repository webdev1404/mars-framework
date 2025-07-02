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
     * Loads a file from the extension's languages dir
     * @param string $file The name of the file to load (must not include the .php extension)
     * @param string $prefix The string's prefix, if any
     * @return static
     */
    public function loadLanguage(string $file = '', string $prefix = '') : static
    {
        if (!$file) {
            $file = $this->name;
        }
        if (!$prefix) {
            $prefix = $this->name;
        }

        $filename = $this->path . '/' . static::DIRS['languages'] . '/' . $this->app->lang->name . '/' . $file . '.php';

        $this->app->lang->loadFilename($filename, $prefix);

        return $this;
    }
}
