<?php
/**
* The isSingleClassTrait Trait
* @package Mars
*/

namespace Mars\Extensions\Traits;

use Mars\App;
use Mars\Extensions\Module;

/**
 * The isSingleClassTrait Trait
 * Trait implementing functionality for extensions consisting of a single class file
 */
trait isSingleClassTrait
{
    /**
     * Builds the extension
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        //extract the plugin's name and module from it's path
        $rc = new \ReflectionClass($this);
        $filename = $rc->getFileName();

        $modules_path = $this->app->extensions_path . '/' . Module::getBaseDir();
        $rel_filename = $this->app->file->getRel($filename, $modules_path);

        $parts = explode('/', $rel_filename);
        
        $module_name = $parts[0];

        $name = '';
        if (count($parts) > 3) {
            $name = $parts[2];
        }

        $this->__constructModule($module_name, $name, $app);
    }

}
