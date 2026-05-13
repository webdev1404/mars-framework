<?php
/**
* The System Menu Class
* @package Mars
*/

namespace Mars\Menus;

use Mars\App;

/**
 * The System Menu Class
 * Base class for the system menus
 */
abstract class System extends Menu
{
    /**
     * Menu Constructor
     * @param App $app The App Instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Adds the Home Link to the Menu
     */
    protected function addHome()
    {
        $this->add(App::__('menu:home'), $this->app->base_url . '/', 'home', priority: 100);
    }

    /**
     * Collects the menu items for the menu
     * @internal
     */
    protected function collectItems()
    {
        //collect the menu items from the modules
        $modules = $this->app->modules->getEnabled();
        foreach ($modules as $name => $path) {
            $module = $this->app->modules->get($name);
            $module->menu($this);
        }

        //collect the menu items from the app
        $app_menu_file = $this->app->app_path . "/menus/{$this->type}.php";
        if (is_file($app_menu_file)) {
            include $app_menu_file;
        }

        $this->app->plugins->run("menu.collect.items.{$this->type}", $this);
    }
}
