<?php
/**
* The Extension's Config Trait
* @package Mars
*/

namespace Mars\Extensions\Modules\Abilities;

/**
 * The Extension's Config Trait
 * Trait which allows extensions to load config files
 */
trait ConfigTrait
{
    /**
     * Loads the config settings from the specified $file and returns it
     * @param string $file The file
     */
    public function loadConfig(?string $file = null)
    {
        $file ??= $this->name . '.php';

        $filename = $this->path . '/' . static::DIRS['config'] . '/' . $file;

        $this->app->config->loadFilename($filename);
    }
}