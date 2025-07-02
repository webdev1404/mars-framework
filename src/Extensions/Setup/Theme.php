<?php
/**
* The Theme Setup Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

/**
 * The Theme Setup Class
 * Setup class for themes
 */
class Theme extends Extension
{
    /**
     * @internal
     */
    protected function getBaseDir() : string
    {
        return \Mars\Extensions\Theme::getBaseDir();
    }

    /**
     * @internal
     */
    protected function getListFilename() : string
    {
        return \Mars\Extensions\Theme::getListFilename();
    }

    /**
     * @internal
     */
    protected function getExtension(string $name) : ?\Mars\Extensions\Theme
    {
        return new \Mars\Extensions\Theme($name, [], $this->app);
    }
}