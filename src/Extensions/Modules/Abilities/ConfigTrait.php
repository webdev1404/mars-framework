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
    public function loadConfig(string $file)
    {
        $file = $file . '.php';
        
        //load the config file from the module's config dir
        $this->app->config->loadFilename($this->path . '/' . static::DIRS['config'] . '/' . $file);

        //load the config file from the config/modules dir
        $this->app->config->loadModule($this->path_rel . '/' . $file);
    }
}