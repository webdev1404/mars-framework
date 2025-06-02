<?php
/**
* The SubModule Single File Extension Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The SubModule Single File Extension Class
 * Base class for all extensions found in the modules folder, which contain a single file. Eg: plugins
 */
abstract class SubModuleSingleFile extends SubModule
{
    /**
     * Builds the extension
     * @param array $params The params passed to the extension, if any
     * @param App $app The app object
     */
    public function __construct(array $params = [], App $app)
    {
        $this->app = $app;

        [$module_name, $name] = $this->getModuleNameAndName();

        parent::__construct($module_name, $name, $params, $app);
    }

    /**
     * Returns the module name and the extension's name
     * @return array
     */
    protected function getModuleNameAndName() : array 
    {
        $rc = new \ReflectionClass($this);

        $modules_path = $this->app->extensions_path . '/' . Module::getBaseDir();
        $rel_filename = $this->app->file->getRel($rc->getFileName(), $modules_path);

        $parts = explode('/', $rel_filename);

        return [$this->getModuleNameFromParts($parts), $this->getExtensionNameFromParts($parts)];
    }

    /**
     * Returns the module name from the parts
     * @param array $parts The parts of the path
     * @return string
     */
    protected function getModuleNameFromParts(array $parts) : string
    {
        return $parts[0];
    }   

    protected function getExtensionNameFromParts(array $parts) : string
    {
        $name = '';
        if (count($parts) > 3) {
            $name = $parts[2];
        }

        return $name;
    }
}