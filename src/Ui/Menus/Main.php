<?php
/**
* The Main Menu Class
* @package Mars
*/

namespace Mars\Ui\Menus;

/**
 * The Main Menu Class
 */
class Main extends System
{
    /**
     * @internal
     */
    public protected(set) string $type = 'main';

    /**
     * Collects the default menu items
     */
    protected function collectItems()
    {
        parent::collectItems();

        $this->addHome();
    }
}
