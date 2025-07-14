<?php
/**
* The Module Setup Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

/**
 * The Module Setup Class
 * Setup class for modules
 */
class Module extends Extension
{
    /**
     * @internal
     */
    protected function getBaseDir() : string
    {
        return \Mars\Extensions\Module::getBaseDir();
    }

    /**
     * @internal
     */
    protected function getListFilename() : string
    {
        return \Mars\Extensions\Module::getListFilename();
    }

    /**
     * @internal
     */
    protected function getExtension(string $name) : ?\Mars\Extensions\Module
    {
        return new \Mars\Extensions\Module($name, [], $this->app);
    }
}
