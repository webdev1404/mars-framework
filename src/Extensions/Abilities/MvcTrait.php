<?php
/**
* The Extension's MVC Trait
* @package Mars
*/

namespace Mars\Extensions\Abilities;

use Mars\App;
use Mars\Mvc\Controller;
use Mars\Mvc\Model;
use Mars\Mvc\View;

/**
 * The Extension's MVC Trait
 * Trait implementing the MVC pattern for extensions
 */
trait MvcTrait
{
    /**
     * @var Controller $controller The currently loaded controller of this extension
     */
    public Controller $controller;

    /**
     * Returns a MVC class name
     * @param string $path The path from where to load the class
     * @param string $class_name The class name
     * @return string The class name
     */
    protected function getMvcClass(string $path, string $class_name) : string
    {
        $namespace_path = str_replace("/", "\\", ucfirst($path) . '/');

        return $namespace_path . App::getClass($class_name);
    }

    /**
     * Loads the controller and returns the instance
     * @param string $controller The name of the controller
     * @param array $allowed_controllers Array with the allowed controller names
     * @return Controller The controller object
     */
    public function getController(string $controller = '', array $allowed_controllers = []) : Controller
    {
        if ($allowed_controllers) {
            if (!in_array($controller, $allowed_controllers)) {
                $controller = '';
            }
        }

        if (!$controller) {
            $controller = $this->name;
        }

        $controller_class = $this->getMvcClass(static::DIRS['controllers'], $controller);

        $class_name = $this->namespace . '\\' . $controller_class;

        $controller = new $class_name($this, $this->app);

        $controller = $this->app->plugins->filter('mvc.controller.get', $controller, $class_name, $this);

        return $controller;
    }
}
