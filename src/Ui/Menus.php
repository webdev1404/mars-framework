<?php
/**
 * The UI's Menus Container Class
 * @package Mars
 */

namespace Mars\Ui;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Ui\Menus\Menu;
use Mars\Ui\Menus\Main as MainMenu;
use Mars\Ui\Menus\Footer as FooterMenu;
use Mars\Ui\Menus\Sidebar as SidebarMenu;

/**
 * The UI's Menus Container Class
 */
class Menus extends \stdClass
{
    use Kernel;
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

        $this->app = $app;
    }
}
