<?php
/**
* The SubModule Extension Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Extensions\Module;
use Mars\Extensions\Extension;
use Mars\Extensions\Modules\Abilities\LanguagesTrait;
use Mars\Extensions\Modules\Abilities\TemplatesTrait;

/**
 * The SubModule Extension Class
 * Base class for all extensions found in the modules folder
 */
abstract class Component extends Extension
{
    use LanguagesTrait {
        LanguagesTrait::loadLanguage as loadLanguageFromTrait;
    }
    use TemplatesTrait;

    /**
     * @var Module $module The parent module of the extension
     */
    public protected(set) Module $module;

    /**
     * @var string $path The path where the extension is located
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }
            
            $this->path = $this->module->path . '/' . static::$base_dir . '/' . $this->name;

            return $this->path;
        }
    }

    /**
     * @var string $assets_path The folder where the assets files are stored
     */
    public protected(set) string $assets_path {
        get {
            if (isset($this->assets_path)) {
                return $this->assets_path;
            }            

            $this->assets_path = $this->module->assets_path . '/' . static::$base_dir . '/' . $this->name;

            return $this->assets_path;
        }
    }

    /**
     * @var string $assets_url The url pointing to the folder where the assets for the extension are located
     */
    public protected(set) string $assets_url {
        get {
            if (isset($this->assets_url)) {
                return $this->assets_url;
            }

            $this->assets_url = $this->module->assets_url . '/' . rawurlencode(static::$base_dir) . '/' . rawurlencode($this->name);

            return $this->assets_url;
        }
    }

    /**
     * @var string $assets_target The path of the assets folder, in the public directory, where the assets for this extension are located.
     */
    public protected(set) string $assets_target {
        get {            
            if (isset($this->assets_target)) {
                return $this->assets_target;
            }

            //module components do not have a target folder
            $this->assets_target = '';

            return $this->assets_target;
        }
    }

    /**
     * @var string $namespace The namespace of the extension
     */
    public protected(set) string $namespace {
        get {
            if (isset($this->namespace)) {
                return $this->namespace;
            }

            $this->namespace =  $this->module->namespace . static::$base_namespace . '\\' . App::getClass($this->name);

            return $this->namespace;
        }
    }

    /**
     * Builds the extension
     * @param string $module_name The name of the module the extension belongs to
     * @param string $name The name of the exension
     * @param array $params The params passed to the extension, if any
     * @param App $app The app object
     */
    public function __construct(string $module_name, string $name, array $params = [], ?App $app = null)
    {
        $this->app = $app ?? App::obj();

        $this->name = $name;
        $this->params = $params;
        $this->module = new Module($module_name, [], $this->app); 
    }

    /**     
     * @see Abilities\LanguagesTrait::loadLanguage()
     * {@inheritdoc}
     */
    public function loadLanguage(string $file = '', string $prefix = '') : static
    {        
        return $this->loadLanguageFromTrait($file, $this->getLanguagePrefix($prefix));
    }

    /**
     * Returns the language prefix for this extension
     * @param string $prefix The prefix to use
     * @return string
     */
    protected function getLanguagePrefix(string $prefix = '') : string
    {
        if ($prefix) {
            return $prefix;
        }

        $prefix = $this->module->name;
        if ($this->name) {
            $prefix.= '.' . $this->name;
        }

        return $prefix;
    }

    /**
     * @see Extension::output()
     * {@inheritdoc}
     */
    public function output()
    {
        $this->app->lang->savePrefix();

        parent::output();

        $this->app->lang->restorePrefix();
    }
}