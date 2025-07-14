<?php
/**
* The Plugin Setup Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

use Mars\LazyLoadProperty;
use Mars\Extensions\Setup\List\Plugins as Reader;

/**
 * The Plugin Setup Class
 * Setup class for plugins
 */
class Plugin extends Extension
{
    #[LazyLoadProperty]
    protected Reader $plugins_reader;

    /**
     * @internal
     */
    protected function getBaseDir() : string
    {
        return \Mars\Extensions\Modules\Plugin::getBaseDir();
    }

    /**
     * @internal
     */
    protected function getListFilename() : string
    {
        return \Mars\Extensions\Modules\Plugin::getListFilename();
    }

    /**
     * @internal
     */
    protected function getExtension(string $name) : ?\Mars\Extensions\Modules\Plugin
    {
        return null;
    }

    /**
     * @see Extension::prepare()
     * {@inheritdoc}
     */
    public function prepare()
    {
        //we only need to create the list of available plugins
        $this->createList();
    }

    /**
     * @see Extension::getList()
     * {@inheritdoc}
     */
    /*protected function getList() : array
    {
        return $this->plugins_reader->get($this->getBaseDir());
    }*/
}
