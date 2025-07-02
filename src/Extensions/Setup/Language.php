<?php
/**
* The Language Setup Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

/**
 * The Language Setup Class
 * Setup class for languages
 */
class Language extends Extension
{
    /**
     * @internal
     */
    protected function getBaseDir() : string
    {
        return \Mars\Extensions\Language::getBaseDir();
    }

    /**
     * @internal
     */
    protected function getListFilename() : string
    {
        return \Mars\Extensions\Language::getListFilename();
    }

    /**
     * @internal
     */
    protected function getExtension(string $name) : ?\Mars\Extensions\Language
    {
        return new \Mars\Extensions\Language($name, [], $this->app);
    }
}