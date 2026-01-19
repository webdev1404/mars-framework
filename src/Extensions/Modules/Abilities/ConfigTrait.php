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
     * @var array $loaded_configs List of already loaded config files
     */
    protected array $loaded_configs = [];

    /**
     * Loads the config settings from the specified $file.
     * @param string $file The file name (without the .php extension)
     */
    public function loadConfig(string $file)
    {
        if (isset($this->loaded_configs[$file])) {
            var_dump($file);
            return;
        }

        $this->loaded_configs[$file] = true;
        
        $file = $file . '.php';
        
        //load the config file from the module's config dir
        $this->app->config->loadFilename($this->path . '/' . static::DIRS['config'] . '/' . $file);

        //load the config file from the config/modules dir
        $this->app->config->loadModule($this->path_rel . '/' . $file);
    }
}
