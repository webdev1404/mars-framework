<?php
/**
* The SubModule Extension Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The SubModule Extension Class
 * Base class for all extensions found in the modules folder
 */
abstract class SubModule extends Module
{
    /**
     * @var string $module_name The name of the module
     */
    public protected(set) string $module_name = '';

    /**
     * @var Module $module The parent module of the extension
     */
    protected Module $module {
        get {
            if (isset($this->module)) {
                return $this->module;
            }

            $this->module = new Module($this->module_name, $this->app);

            return $this->module;
        }
    }

    /**
     * Builds the extension
     * @param string $module_name The name of the module the extension belongs to
     * @param string $name The name of the exension
     * @param App $app The app object
     */
    public function __construct(string $module_name, string $name = '', ?App $app = null)
    {
        $this->app = $app ?? $this->getApp();

        if (!$name) {
            $parts = explode('/', $module_name);
            if (count($parts) > 1) {
                [$module_name, $name] = $parts;
            }
        }

        $this->module_name = $module_name;
        $this->name = $name;        
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\Abilities::loadLanguage()
     */
    public function loadLanguage(string $file = '', ?string $prefix = null) : static
    {
        if ($prefix === null) {
            $prefix = $this->module_name . '.';

            if ($this->name) {
                $prefix.= $this->name . '.';
            }
        }

        return parent::loadLanguage($file, $prefix);
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootPath()
     */
    protected function getRootPath() : string
    {
        return $this->module->path;
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootUrl()
     */
    protected function getRootUrl() : string
    {
        return $this->module->url;
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootUrlStatic()
     */
    protected function getRootUrlStatic() : string
    {
        return $this->module->url_static;
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootNamespace()
     */
    protected function getRootNamespace() : string
    {
        return '\\' . Module::getBaseNamespace() . '\\' . App::getClass($this->module_name);
    }
}