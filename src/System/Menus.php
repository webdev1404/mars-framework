<?php
/**
* The System's Menus Container Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Menu\Menu;
use Mars\Menu\Main as MainMenu;
use Mars\Menu\Footer as FooterMenu;
use Mars\Menu\Sidebar as SidebarMenu;

/**
 * The System's Menus Container Class
 */
class Menus extends \stdClass
{
    use LazyLoad;

    /**
     * @var MainMenu $main The Main Menu
     */
    #[LazyLoadProperty]
    public MainMenu $main;

    /**
     * @var FooterMenu $footer The Footer Menu
     */
    #[LazyLoadProperty]
    public FooterMenu $footer;

    /**
     * @var SidebarMenu $sidebar The Sidebar Menu
     */
    #[LazyLoadProperty]
    public SidebarMenu $sidebar;

    /**
     * Builds the Menus object
     * @param App $app The App Instance
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);
    }
}
